<?php
                $sql = "SELECT * from referout WHERE refer_date = '2015-06-19 00:00:00'";

                $rawData = Yii::$app->db->createCommand($sql)->queryAll();
                $main_data = [];
                foreach ($rawData as $data) {
                    $main_data[] = [
                        'referid_source' => $data['refer_no'],
                        'd_update' => $data['save_date'],
                        'hosp_source' => $data['from_hospcode'],
                        'hospcode' => $data['refer_hospcode'],
                    ];
                }
                $main = json_encode($main_data);
                
                print_r($main);

?>
<hr>
<?php

	//เปิดใช้งานฟังก์ชัน SoapClient ที่มีอยู่ใน appserv โดยแก้ไขไฟล์ php.ini ลบเครื่องหมาย ; หน้าบรรทัดที่เขียนว่า extension=php_soap.dll ออก
	$client = new SoapClient("http://203.157.103.30/nrefer/webservicejson.asmx?WSDL");

	
	//$imagedata = file_get_contents("Test.jpg");
             // alternatively specify an URL, if PHP settings allow
	//$base64 = base64_encode($imagedata);
	
 	//$json = '[ { id: "00", "refer_no": "Test123456", "refer_picture": "'.$base64.'" }]';
    
      //$json = '[ {"hospcode": "10705", "referid_source": "10705002", "hosp_source": "11036" ,"d_update": "2015-11-19"}]';
		

	$params = array(
					'user' => 'user',
					'pass' => 'pass',
					'tableName' => 'refer_result',
					'json' => $main);
	$result = $client->DynamicInsertDB($params)->DynamicInsertDBResult;
	echo $result;
	
?>