<?php
// Include FPDF autoload file or class
require_once('vendor/fpdf/fpdf.php');

// Include FPDI autoload file
require_once('vendor/FPDI/src/autoload.php');

// Include FPDF autoload file or class
require_once('vendor/fpdf/fpdf.php');

// Include FPDI autoload file
require_once('vendor/FPDI/src/autoload.php');

// Usage example
$pdfFile = 'C:/xampp/htdocs/watermark/assets/pdf/document.pdf';
$imageFile = 'assets/signature/Macalino_Esignature.png';
$text = 'Prince Allyson Macalino';
$outputFile = 'output/output.pdf'; // Adjust the output path as needed

// Function to add watermark to the second page of a PDF
function addWatermarkToSecondPage($pdfFile, $imageFile, $text, $outputFile) {
    $pdf = new setasign\Fpdi\Fpdi(); // Create FPDI instance

    // Import the source PDF and get the page count
    $pageCount = $pdf->setSourceFile($pdfFile);

    // Iterate through each page of the PDF
    for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
        $tplIdx = $pdf->importPage($pageNum); // Import the current page

        // Add the page without any changes
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx);

        // Add the watermark only to the second page
        if ($pageNum === 2) {
            // Calculate the new width and height based on 50x reduction
            $newWidth = 210 / 13; // Assuming original width is 210mm
            $newHeight = 297 / 13; // Assuming original height is 297mm

            // Specify custom x and y coordinates for the image position
            $xCoordinate = 130; // Adjust this value as needed
            $yCoordinate = 170; // Adjust this value as needed

            // Add the image as a watermark at the specified coordinates
            $pdf->Image($imageFile, $xCoordinate, $yCoordinate, $newWidth, $newHeight, 'PNG', '', '', false, 300, '', false, false, 0);

            // Add the text watermark
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetXY(85, 192); // Set the position for the text
            $pdf->Cell(0, 0, $text, 0, 1, 'C');
        }
    }

    // Save the modified PDF to the output folder with the specified output file name
    $pdf->Output($outputFile, 'F');
}

// Usage example: Add watermark to the second page of the PDF
addWatermarkToSecondPage('C:/xampp/htdocs/watermark/assets/pdf/document.pdf', 'assets/signature/Macalino_Esignature.png', 'Prince Allyson Macalino', 'output/output.pdf');

echo 'Watermark added to the second page successfully!';

?>







<?php
require_once("../config/db_connection.php");
require_once('vendor/fpdf/fpdf.php');
require_once('vendor/FPDI/src/autoload.php');

use setasign\Fpdi\Fpdi;

// Sanitize inputs
$document_id = mysqli_real_escape_string($conn, $_POST['document_id']);
$signatoryIds = array_map('intval', $_POST['signatoryIds']); // Sanitize signatory IDs

foreach ($signatoryIds as $signatoryId) {
    // Query to fetch the signature filename from the users table
    $query = "SELECT name, signature FROM users WHERE user_id = '$signatoryId'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $signatureFilename = $row['signature'];
        $sourcePath = "upload_folder/$signatureFilename"; // Path to the signature image
        $destinationPath = "documents_with_signatures/$signatureFilename"; // Destination folder

        // Copy the signature image to the destination folder
        if (copy($sourcePath, $destinationPath)) {
            echo "Signature for user ID $signatoryId copied successfully.<br>";
        } else {
            echo "Error copying signature for user ID $signatoryId.<br>";
        }
    } else {
        echo "No signature found for user ID $signatoryId.<br>";
    }
}


// Fetch document type
$documentType = "";
$queryLastDocumentType = "SELECT d.document_type 
                          FROM assign_signatory a 
                          JOIN documents d ON d.document_id = a.document_id 
                          WHERE a.signatory_id = '$signatoryIds[0]' 
                          ORDER BY a.assign_id DESC LIMIT 1";
$resultLastDocumentType = mysqli_query($conn, $queryLastDocumentType);
if ($rowLastDocumentType = mysqli_fetch_assoc($resultLastDocumentType)) {
    $documentType = $rowLastDocumentType['document_type'];
}

// Retrieve PDF file and initiate FPDI
$pdf = new FPDI();
$queryDoc = "SELECT document_id, document FROM documents WHERE document_id = '$document_id'";
$resultDoc = mysqli_query($conn, $queryDoc);

if ($rowDoc = mysqli_fetch_assoc($resultDoc)) {
    $documentId = $rowDoc['document_id'];
    $documentFilename = $documentId . '_' . $rowDoc['document']; // Construct the document filename

    $pdf->setSourceFile("../assets/documents/$documentFilename");

    $pageCount = $pdf->getNumberOfPages(); // Get the number of pages in the PDF

    // Loop through each page of the PDF
    for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
        $pdf->AddPage();
        $templateId = $pdf->importPage($pageNum);
        $pdf->useTemplate($templateId);

        // Define arrays for x and y coordinates based on document type
        $xSignatureCoordinates = [];
        $ySignatureCoordinates = [];
        $xNameCoordinates = [];
        $yNameCoordinates = [];

            // Define arrays for x and y coordinates for each signatory's signature and name
    if ($documentType == "Thesis/Dissertation Concept Paper Adviser Endorsement Form") {
        $xSignatureCoordinates = [120, 100]; // X coordinates for each signatory's signature
        $ySignatureCoordinates = [175, 222]; // Y coordinates for each signatory's signature
        $xNameCoordinates = [130, 100]; // X coordinates for each signatory's name
        $yNameCoordinates = [194, 223]; // Y coordinates for each signatory's name
    } 
    elseif ($documentType == "Thesis/Dissertation Advising Contract") {
        $xSignatureCoordinates = [150, 120, 170]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120, 170]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253,253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Consultation/Monitoring Form") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Adviser Appointment and Acknowledgement Form") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Confidentiality Non-Disclosure Agreement") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Committee Appointment and Acceptance Form") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Proposal Defense Endorsement Form") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Proposal Defense Evaluation Sheet") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Final Endorsement Form") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Pre-Final Evaluation Sheet") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Panel on Oral Defense Appointment and Acceptance Form") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    elseif ($documentType == "Thesis/Dissertation Final Endorsement Form") {
        $xSignatureCoordinates = [150, 120]; // Different X coordinates for type2
        $ySignatureCoordinates = [200, 250]; // Different Y coordinates for type2
        $xNameCoordinates = [160, 120]; // Different X coordinates for type2
        $yNameCoordinates = [220, 253]; // Different Y coordinates for type2
    }
    else {
        // Default coordinates
        $xSignatureCoordinates = [100, 80];
        $ySignatureCoordinates = [150, 200];
        $xNameCoordinates = [110, 80];
        $yNameCoordinates = [170, 203];
    }


        // Loop through signatory IDs to insert signatures and names
        foreach ($signatoryIds as $index => $signatoryId) {
            // Fetch and insert signature image (omitted for brevity)

            // Check if coordinates are defined for the current signatory
            if (isset($xSignatureCoordinates[$index]) && isset($ySignatureCoordinates[$index]) && isset($xNameCoordinates[$index]) && isset($yNameCoordinates[$index])) {
                $xSignature = $xSignatureCoordinates[$index];
                $ySignature = $ySignatureCoordinates[$index];
                $xName = $xNameCoordinates[$index];
                $yName = $yNameCoordinates[$index];

                // Insert signature image and name
                $pdf->Image($signaturePath, $xSignature, $ySignature, 30);
                $pdf->SetFont('Arial', '', 12);
                $pdf->Text($xName, $yName, $userName);
            } else {
                echo "Coordinates not defined for signatory ID $signatoryId.<br>";
            }
        }
    }

    $pdfFilename = $_SERVER['DOCUMENT_ROOT'] . '/wdfuip/admin/signatured_documents/' . $document_id . '.pdf';
    $pdf->Output($pdfFilename, 'F');

    echo "PDF with signatures saved successfully.";
} else {
    echo "Document not found.";
}


?>




