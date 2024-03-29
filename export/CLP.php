<?php
include_once "../database.php";
include_once "../fungsi.php";
include_once "fpdf16/fpdf.php";
$IdProyek = $_REQUEST['proyekId'];
$person = $_REQUEST['person'];
//object database class
$db_object = new database();

$proyek_row = $db_object->find_in_table("proyek", "*", "WHERE id = " . $IdProyek);

$sql_que = "SELECT
                hasil.*,
                kontraktor.`nama_kontraktor`
              FROM
                hasil,
                kontraktor
              WHERE 
              hasil.`kontraktor_id` = kontraktor.`id`
              AND proyek_id = ".$IdProyek."
              ORDER BY nilai_v DESC";

$db_query = $db_object->db_query($sql_que) or die("Query gagal");
//Variabel untuk iterasi
$i = 0;
//Mengambil nilai dari query database
while ($data = $db_object->db_fetch_array($db_query)) {
    $cell[$i][0] = $data['nama_kontraktor'];
    $cell[$i][1] = price_format($data['nilai_v']);
    $i++;
}

//memulai pengaturan output PDF
class PDF extends FPDF {

    //untuk pengaturan header halaman
    function Header() {
        
        //Pengaturan Font Header
        $this->SetFont('Times', 'B', 14); //jenis font : Times New Romans, Bold, ukuran 14
        //untuk warna background Header
        $this->SetFillColor(255, 255, 255);
        //untuk warna text
        $this->SetTextColor(0, 0, 0);
        //Menampilkan tulisan di halaman
        //$this->Cell(25, 1, 'Laporan Hasil Analisa', '', 0, 'C', 1); //TBLR (untuk garis)=> B = Bottom,
        // L = Left, R = Right
        //untuk garis, C = center
    }

}

//pengaturan ukuran kertas P = Portrait
$pdf = new PDF('P', 'cm', 'A4');
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Times', 'B', 12);
//Ln() = untuk pindah baris

$imagenurl = '../assets/images/logo.jpg';
$pdf->Cell(0, 0, $pdf->Image($imagenurl, 1,1,3,2), 0, 0, 'C', false,'');

$pdf->Ln();
$pdf->Cell(18, 1, 'LAPORAN HASIL PERHITUNGAN', '', 0, 'C'); //TBLR (untuk garis)=> B = Bottom,
$pdf->Ln();
$pdf->Cell(18, 1, 'PENILAIAN KARYAWAN TERBAIK', 'B', 0, 'C'); //TBLR (untuk garis)=> B = Bottom,
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(18, 1, 'Nama Posisi: '.$proyek_row['nama_proyek'], '', 0, 'L'); //TBLR (untuk garis)=> B = Bottom,
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(1, 1, 'No', 'LRTB', 0, 'C');
$pdf->Cell(14, 1, 'Karyawan', 'LRTB', 0, 'C');
$pdf->Cell(3, 1, 'Nilai', 'LRTB', 0, 'C');
$pdf->Ln();
$pdf->SetFont('Times', "", 10);
for ($j = 0; $j < $i; $j++) {
    //menampilkan data dari hasil query database
    $pdf->Cell(1, 1, $j + 1, 'LBTR', 0, 'C');
    $pdf->Cell(14, 1, $cell[$j][0], 'LBTR', 0, 'L');
    $pdf->Cell(3, 1, $cell[$j][1], 'LBTR', 0, 'C');
    $pdf->Ln();
}

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(18, 1, 'Mengetahui,', '', 0, 'R'); 
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(18, 1, $person, '', 0, 'R'); 
$pdf->Ln();
$pdf->Cell(18, 1, 'Manager', '', 0, 'R'); 
//menampilkan output berupa halaman PDF
$pdf->Output();
?>