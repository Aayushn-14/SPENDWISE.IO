<?php

$uploadDir = 'C:/xampp/htdocs/del/uploads/';
$allowedExtensions = ['pdf', 'xlsx', 'xls'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['file']['name'])) {
        $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        // Check file extension 
        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
            $uniqueFilename = uniqid() . '_' . $_FILES['file']['name'];
            $uploadPath = $uploadDir . $uniqueFilename;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                $pythonScript = '';
                $pythonScript2 = '';

                if (in_array(strtolower($fileExtension), ['xlsx', 'xls'])) {
                    $pythonScript = "./python/xl_dtl_ext.py";
                    $pythonScript2 = "./python/xl_tbl_ext.py";
                } elseif (strtolower($fileExtension) === 'pdf') {
                    $pythonScript = "./python/pf_dtl_ext.py";
                    $pythonScript2 = "./python/pf_tbl_ext.py";
                }

                if (!empty($pythonScript) && !empty($pythonScript2)) {

                    exec("python.exe $pythonScript \"$uploadPath\" 2>&1", $output, $returnCode);
                    exec("python.exe $pythonScript2 \"$uploadPath\" 2>&1", $output, $returnCode);
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
            
                    } else {
                        echo json_encode(['message' => 'Error processing the file']);
                    }
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

// Rest of your code remains the same

// Determine which HTML file to redirect to based on $analysisPeriod
$analysisPeriod = $_POST['analysisMethod'];
if ($analysisPeriod === 'Day-Wise') {
    // Redirect to the appropriate HTML file for weekly analysis
    header("Location: res-daily.html");
    exit();
} elseif ($analysisPeriod === 'Month-Wise') {
    // Redirect to the appropriate HTML file for monthly analysis
    header("Location: res-monthly.html");
    exit();
} elseif ($analysisPeriod === 'Year-Wise') {
    // Redirect to the appropriate HTML file for yearly analysis
    header("Location: res-yearly.html");
    exit();
} else {
    echo json_encode(['message' => 'Invalid analysis period']);
    exit();
}

//header("Location: result1.html");
exit();
?>
