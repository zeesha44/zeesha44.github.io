<?php
require_once 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $stateChapter = $_POST['stateChapter'];
        $listingType = $_POST['listingType'];
        $schoolName = $_POST['schoolName'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Validate state exists
        $stmt = $conn->prepare("SELECT id FROM states WHERE name = :state_name");
        $stmt->execute([':state_name' => $stateChapter]);
        $state = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$state) {
            throw new Exception('Invalid state selected');
        }

        // Create uploads directory if it doesn't exist
        $uploadsDir = __DIR__ . '/uploads';
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        // Handle logo upload
        $logoPath = '';
        if (isset($_FILES['schoolLogo']) && $_FILES['schoolLogo']['error'] === UPLOAD_ERR_OK) {
            $logoInfo = pathinfo($_FILES['schoolLogo']['name']);
            $logoExt = strtolower($logoInfo['extension']);
            
            // Validate image type
            if (!in_array($logoExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new Exception('Invalid logo file type. Only JPG, PNG, and GIF are allowed.');
            }
            
            $logoName = uniqid('logo_') . '.' . $logoExt;
            $logoPath = 'uploads/' . $logoName;
            
            if (!move_uploaded_file($_FILES['schoolLogo']['tmp_name'], $logoPath)) {
                throw new Exception('Failed to upload logo');
            }
        }

        // Begin transaction
        $conn->beginTransaction();

        // Insert into schools table
        $stmt = $conn->prepare("INSERT INTO schools (state_id, listing_type, school_name, address, phone, email, username, password, logo_path) 
                              VALUES (:state_id, :listing_type, :school_name, :address, :phone, :email, :username, :password, :logo_path)");
        
        $stmt->execute([
            ':state_id' => $state['id'],
            ':listing_type' => $listingType,
            ':school_name' => $schoolName,
            ':address' => $address,
            ':phone' => $phone,
            ':email' => $email,
            ':username' => $username,
            ':password' => $password,
            ':logo_path' => $logoPath
        ]);

        $schoolId = $conn->lastInsertId();

        // Handle multiple school images
        if (isset($_FILES['schoolImages'])) {
            $stmt = $conn->prepare("INSERT INTO school_images (school_id, image_path) VALUES (:school_id, :image_path)");
            
            foreach ($_FILES['schoolImages']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['schoolImages']['error'][$key] === UPLOAD_ERR_OK) {
                    $imageInfo = pathinfo($_FILES['schoolImages']['name'][$key]);
                    $imageExt = strtolower($imageInfo['extension']);
                    
                    // Validate image type
                    if (!in_array($imageExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                        continue; // Skip invalid file types
                    }
                    
                    $imageName = uniqid('img_') . '.' . $imageExt;
                    $imagePath = 'uploads/' . $imageName;
                    
                    if (move_uploaded_file($tmp_name, $imagePath)) {
                        $stmt->execute([
                            ':school_id' => $schoolId,
                            ':image_path' => $imagePath
                        ]);
                    }
                }
            }
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true, 
            'message' => 'Registration successful! Your application is pending approval.'
        ]);

    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Method not allowed'
    ]);
}
?>
