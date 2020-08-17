<?php


$veri = file_get_contents("https://www.udemy.com/sifirdan-ileri-seviye-web-programlama-html-php-pdo-mysql"); 

preg_match_all('@<div data-purpose="enrollment">(.*?) (.*?)students@si',$veri,$dlinkcek);


/*

echo "<pre>";
print_r($dlinkcek);
echo "</pre>";
*/


$toplam=$dlinkcek[2][0];



?>
<!DOCTYPE html>
<html>
<head>
	<title>Udemy Oto Satış Kontrolü</title>
	<meta charset="utf-8">
	
	<!-- Yönlendirme Kodu -->
	<meta http-equiv="refresh" content="300;URL=cek.php">
</head>
<body>
	<br>
	<br>
	<b style="font-size:400px;"><center><?php echo $toplam ?><center></b>

	<?php

	$body="<h1>ÖĞRENCİ KAYDI YAPILDI<h1>";

	
	//text okuma işlemleri
	
	$dosya = "veri.txt";  
	$handle = fopen($dosya, "r");  
	$icerik = fread($handle, filesize($dosya));

	//gelen veriyi parçalama
	$listele=explode(' ', $icerik);
	fclose($handle); 

	$sat=(int)end($listele);

	//kontrol

	if ($toplam>$sat) {

		

		//Sms Gönder
		//KENDİNİZE GÖRE ÖZELLEŞTİRMEDEN ÇALIŞMAZ goner.php içerisinde kullanıcı adı şifre boş doldurun

			$text="$toplam. kurs kaydi yapildi simdi.";

			//Sms Gönderim İşlemi
			include 'gonder.php';

			$result = sendRequest('http://api.iletimerkezi.com/v1/send-sms',$xml,array('Content-Type: text/xml'));
		
		//die('<pre>'.var_export($result,1).'</pre>');


		?>



		<h1 style="font-size:100px; color:green;"><center>New Student Yuppi!<center></h1>


		<audio  autoplay>
			<source src="money.mp3" type="audio/mpeg">
				Your browser does not support the audio element.
			</audio>

			<?php 
		//txt veri yazma işlemleri

			$fh = fopen($dosya, 'w') or die("Açılamadı.!");  
			$veri = " ".trim($toplam);
			fwrite($fh, $icerik.$veri);  
			fclose($fh); 

		//mail gönderim işlemleri
			include '../nedmin/netting/baglan.php';
			require("../mailphp/class.phpmailer.php");

			$ayarsor=$db->prepare("select * from ayar where ayar_id=?");
			$ayarsor->execute(array(0));
			$ayarcek=$ayarsor->fetch(PDO::FETCH_ASSOC);


			$mail = new PHPMailer();

			$mail->IsSMTP();  
			$mail->Host     = $ayarcek['ayar_smtphost']; 
			$mail->SMTPAuth = true;    
			$mail->Username = $ayarcek['ayar_smtpuser'];  
			$mail->Password = $ayarcek['ayar_smtppassword'];
			$mail->Port     = $ayarcek['ayar_smtpport'];
			$mail->From     = $ayarcek['ayar_smtpuser']; 
			$mail->Fromname = "Öğrenci Kaydı";
			$mail->AddAddress("info@emrahyuksel.com.tr","Sipariş");
			$mail->Subject  =  "$toplam. Öğrenci Kaydı";
			$mail->Body     =  $body;
			$mail->IsHTML(true); 
			$mail->CharSet = 'UTF-8';

			if($mail->Send())
			{
	//echo "Mesaj Gönderildi <p>";
				exit;
			} 






		} else {?>

		<h1 style="font-size:100px; color:red;"><center>No Student!<center></h1>


		<?php }
		?>


	</body>
	</html>
