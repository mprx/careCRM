<?php
require_once('pdf/tcpdf_include.php');
require_once('pdf/tcpdf.php');
require_once('connectvars.php');
include_once('startsession.php');
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

if(!isset($_SESSION["userid"]))
{
    echo 'Bitte <a href="login.php">loggen Sie sich ein</a>, um auf diese Seite zugreifen zu können.';
    exit();
}


//Klasse und Methode für Footer
class MYPDF extends TCPDF {

    public function Footer() {
        // Schriftart
        $this->SetFont('helvetica', '', 10);
        // Seitennummer
        $this->SetY(-35);
        //$this->Cell(0, 0, "", 'T', 0, 'L');
        $this->Cell(0, 10, "CareCRM GmbH", 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetY(-30);
        $this->Cell(0, 10, "Firmenstraße 12, 1110 Wien", 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetY(-25);
        $this->Cell(0, 10, "M: firma@gmail.com   |   T: +43 660 1234555", 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetY(-20);
        $this->Cell(0, 10, "IBAN: AT00 1234 5678 9123 4567   |   BIC: XXXATABCD", 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetY(-40);
        $this->Cell(0, 10, 'Seite '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), "T", false, 'R', 0, '', 0, false, 'T', 'R');
    }
}

//Rechnungsdaten:
if(isset($_GET["rg_id"]))
{
    $rg_id = mysqli_real_escape_string($dbc, trim($_GET['rg_id']));
    $query = "SELECT kd_id, rg_jahr, rg_nr, rg_anschrift, DATE_FORMAT(rg_zeitraum_start,'%d.%m.%Y') AS rg_zeitraum_start, DATE_FORMAT(rg_zeitraum_ende,'%d.%m.%Y') AS rg_zeitraum_ende, DATE_FORMAT(rg_datum,'%d.%m.%Y') AS rg_datum, rg_art FROM rechnung WHERE rechnung.rg_id = '$rg_id'";
    $data = mysqli_query($dbc,$query)
        or die(erroglo($dbc,$query));
    if(mysqli_num_rows($data) != 0)
    {
        $row = mysqli_fetch_array($data);
        $kd_id = $row["kd_id"];
        $rg_jahr = $row["rg_jahr"];
        $rg_nr = str_pad($row["rg_nr"], 4, '0', STR_PAD_LEFT);
        $rg_satz = "Wir erlauben uns, Ihnen folgende Leistung in Rechnung zu stellen:";
        $rg_zahlungsziel = "Zahlungsziel: 14 Tage ab Erhalt der Rechnung";
        if($row["rg_art"] == 'g')
        {
            $rg_nr = "GS-". $rg_nr;
            $rg_satz = "Die ist eine <b>Gutschrift</b> für unsere Rechnung mit der <b>Nr. " . substr($rg_nr,3) . "-$rg_jahr</b>.";
            $rg_zahlungsziel = "Falls Sie die Rechnung " . substr($rg_nr,3) . "-$rg_jahr bereits bezahlt haben, bitten wir Sie um Kontaktaufnahme. Ansonsten betrachten Sie diese bitte als Gegenstandslos.";
        }
        $rg_anschrift = $row["rg_anschrift"];
        $rg_zeitraum_start = $row["rg_zeitraum_start"];
        $rg_zeitraum_ende = $row["rg_zeitraum_ende"];
        $rg_zeitraum = $rg_zeitraum_start;
        if($rg_zeitraum_ende != $rg_zeitraum_start) $rg_zeitraum = $rg_zeitraum_start . " bis " . $rg_zeitraum_ende;

        $rg_datum = $row["rg_datum"];
        $rg_anschrift_explode = explode("\n", $rg_anschrift);
        $rg_anschrift_html = "";
        foreach($rg_anschrift_explode  as $value)
        {
            $rg_anschrift_html = $rg_anschrift_html . $value . "<br>";
        }

        $query2 = "SELECT * FROM positionen, leistung WHERE positionen.rg_id = '$rg_id' AND leistung.lg_id = positionen.lg_id ORDER BY positionen.po_nr";
        $data2 = mysqli_query($dbc,$query2)
        or die(erroglo($dbc,$query2));

        if(mysqli_num_rows($data2) != 0)
        {
            $leistung_html = "";
            $rg_gesamtsumme = 0;
            while($row2 = mysqli_fetch_array($data2))
            {
                $lg_bezeichnung = $row2["lg_bezeichnung"];
                $lg_einheit = $row2["lg_einheit"];
                $lg_rabattgrenze = $row2["lg_rabattgrenze"];
                $lg_einzeltarif = $row2["lg_einzeltarif"];
                $lg_mengentarif = $row2["lg_mengentarif"];
                $po_anzahl = $row2["po_anzahl"];

                $preis = $lg_einzeltarif;
                if($po_anzahl >= $lg_rabattgrenze && !empty($lg_rabattgrenze)) $preis = $lg_mengentarif;



                $gesamt = round($preis*$po_anzahl,2);
                $preis = number_format($preis, 2, ',', '.');
                $rg_gesamtsumme = $rg_gesamtsumme + $gesamt;
                $gesamt = number_format($gesamt, 2, ',', '.');


                $leistung_html = $leistung_html . "
                                <tr>
                                <td>$lg_bezeichnung</td>
                                <td align=\"right\">$po_anzahl</td>
                                <td align=\"right\">$lg_einheit</td>
                                <td align=\"right\">$preis</td>
                                <td align=\"right\">$gesamt</td>
                                </tr>
                                ";
            }
            $rg_gesamtsumme = number_format($rg_gesamtsumme, 2, ',', '.');
        }
        else
        {
            echo "keine Daten für diese Rechnung vorhanden!";
            exit();
        }


    }
    else
    {
        echo "keine Daten für diese Rechnung vorhanden!";
        exit();
    }
}
else
{
    echo "Ungültiger Link!";
    exit();
}

// Neues Dokument
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Mario Praxmarer');
$pdf->SetTitle('Rechnung');

$pdf->SetPrintHeader(true);
$pdf->SetHeaderData('CareCRM_logo.png', 20, '', "");

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetFont('dejavusans', '', 10);


//Erstellung:

$pdf->AddPage();

$html = "
<br><br><br>
<table border=\"0\" cellpadding=\"20\">
	<tr>
	    <td>An<br>$rg_anschrift_html
        </td>
        <td align=\"right\">
            <table border=\"0\">
                <tr>
                    <td colspan=\"2\">Wien, am $rg_datum<br></td>
                </tr>
                <tr>
                    <td>Rechnungsnummer:</td>
                    <td>$rg_nr" . "-" . "$rg_jahr</td>
                </tr>
                <tr>
                    <td>Kundennummer:</td>
                    <td>$kd_id</td>
                </tr>
            </table>
	        
        </td>
    </tr>
    <tr>
	    <td colspan=\"2\">
	    <br><br><br><br><br>$rg_satz
        </td>
    </tr>
</table>
<br>
<br>
<table border=\"0\" cellspacing=\"10\">
    <tr>
        <th width=\"290\"><b>Bezeichnung</b></th>
        <th width=\"50\" align=\"right\"><b>Anzahl</b></th>
        <th width=\"50\" align=\"right\"><b>Einheit</b></th>
        <th width=\"120\" align=\"right\"><b>Einzelpreis (€)</b></th>
        <th width=\"80\" align=\"right\"><b>Gesamt (€)</b></th>
    </tr>
	$leistung_html
	
	<tr>
	    <td colspan=\"4\" align=\"right\"><br><h4>Gesamt:</h4></td>
	    <td align=\"right\"><br><h4>$rg_gesamtsumme</h4></td>
    </tr>
</table>
<br>
<br>
<br>
<table border=\"0\">
<tr>
<td>Zeítraum: $rg_zeitraum</td>
<td align=\"right\">(Leistung im Rahmen des GuKG - USt- befreit)</td>
</tr>
</table>
<br>
<br>
<br>

<table>
<tr>
<td>$rg_zahlungsziel</td>
</tr>
<tr>
<td><br><br><br>Mit freundlichen Grüßen und bestem Dank für Ihr Vertrauen</td>
</tr>
</table>
";

//Output
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->lastPage();

$pdf->Output('rechnung.pdf', 'I');

