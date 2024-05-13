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

echo 'Watermark added to the second page successfully!' .' Good job';
echo "this is 2nd branch";
echo "Added text before merging the new branch";

?>



