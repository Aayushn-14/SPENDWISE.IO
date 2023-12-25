<?php
ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", "temp/error_log.txt");
date_default_timezone_set('Asia/Kolkata');

$uploadDir = 'C:/xampp/htdocs/del/uploads/';
$allowedExtensions = ['xlsx', 'xls', 'pdf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['file']['name'])) {
        $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        // Check file extension 
        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
            $uniqueFilename = uniqid() . '_' . $_FILES['file']['name'];
            $uploadPath = $uploadDir . $uniqueFilename;
            //move the file
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                // Execute the Python script to process the uploaded file
                // $pythonScript = "extract-v5.py";
                // exec("python.exe $pythonScript \"$uploadPath\" 2>&1", $output, $returnCode);
                // $pythonScript = "tbulaV4.py";
                // exec("python.exe $pythonScript \"$uploadPath\" 2>&1", $output, $returnCode);
                // $pythonScript = "insert-dataV3.py";
                // exec("python.exe $pythonScript \"$uploadPath\" 2>&1", $output, $returnCode);
                $pythonScript = "in-details.py";
                exec("python.exe $pythonScript \"$uploadPath\" 2>&1", $output, $returnCode);
                
                echo "Python Script Output:\n";
                foreach ($output as $line) {
                    echo $line . "\n";
                }
                echo "Return Code: $returnCode\n";

                if ($returnCode !== 0) {
                    echo json_encode(['message' => 'Error executing Python script']);
                    exit;
                }

                if (is_array($output) && in_array("Processed and inserted into the database successfully", $output)) {
                    echo json_encode(['message' => 'File uploaded and processed successfully']);
                } else {
                    echo json_encode(['message' => 'Error processing the file']);
                }
            } else {
                echo json_encode(['message' => 'Error uploading the file']);
            }
        } else {
            echo json_encode(['message' => 'Invalid file type']);
        }
    } else {
        echo json_encode(['message' => 'No file selected']);
    }
} else {
    echo json_encode(['message' => 'Invalid request']);
}

header("Location: result1.html");
exit();
?>
