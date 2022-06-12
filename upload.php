<?php
//
date_default_timezone_set('Africa/Lagos');
//
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//Creating Array for JSON response
$response = array();
//
//Replace xxxx with your parameters
$servername = '127.0.0.1:3306';
$username = 'xxxxx';
$password = 'xxxxx';
$dbname = 'xxxxx';
$tablename = 'xxxxx';           
//
// Check if we got the field from the user
if (isset($_GET['serial_no']) && isset($_GET['height']) && isset($_GET['voltage']) && isset($_GET['httpResponse']) && isset($_GET['temperature'])) {
    //
    $voltageValue = $_GET['voltage'];
    $serialValue = $_GET['serial_no'];
	$heightValue = $_GET['height'];
	$httpResponseValue = $_GET['httpResponse'];
	$temperatureValue = $_GET['temperature'];
	$time = date('H:i:s');
	$date = date('d-m-Y');
	$month = date('F', strtotime($date));
	$year = date('Y', strtotime($date));
	$recordMonth = $month.' '.$year;
	//
	$connVar1 = mysqli_connect($servername,$username,$password,$dbname);
    //
	// Fire SQL query to select data from weather
	$check = "SELECT * FROM $tablename WHERE serial_no = '$serialValue'";
	$result1 = mysqli_query($connVar1, $check);
	//
    if (!empty($result1)){
		//Check if user exist
		if (mysqli_num_rows($result1) > 0){
			//Client ID Already Exist
			//
			$result2 = mysqli_query($connVar1, "SELECT * FROM $serialValue");
			//
			if($result2){
			    //
			    $rows = mysqli_num_rows($result2);
			    //
                if($rows > 0){
			        // Check for successful execution of query
			        if ($rows >= 10) {
			            $result3 = mysqli_query($connVar1, "DELETE FROM $serialValue LIMIT 9");
			            //
			            if($result3){
			                //Save History File
			                $resultz = mysqli_query($connVar1, "SELECT tankVolume,timeValue FROM $serialValue");
                            if(mysqli_num_rows($resultz) > 0){
                                while($rowz = $resultz->fetch_assoc()) {
			                        $readVolume = $rowz['tankVolume'];
			                        $readTime = $rowz['timeValue'];
			                        $path = $_SERVER['DOCUMENT_ROOT'].'/'.$clientID.'/history/'.$recordMonth.'/'.$date;
			                        if (!file_exists($path) && !is_dir($path)) {
                                        mkdir($path, 0777, true);
                                    }
			                        $filename = $serialValue.".txt";
			                        $fullPath = $path."/".$filename;
			                        if(file_exists($fullPath)){
			                            $content = ",".$readVolume."-".$readTime;
			                            $fp = fopen($fullPath,"a+");
                                        fwrite($fp,$content);
                                        fclose($fp);
			                        }
			                        if(!file_exists($fullPath)){
			                            $content = $readVolume."-".$readTime; 
			                            $fp = fopen($fullPath,"a+");
                                        fwrite($fp,$content);
                                        fclose($fp);
			                        }
                                }
                            }
			            }
			        } 
			        else{
			            //
			            $resulta = mysqli_query($connVar1, "SELECT * FROM $tablename WHERE serial_no = '$serialValue'");
			            if($resulta){
			                //Extract Saved Height And Width Values From table
			                $row = mysqli_fetch_assoc($resulta);
			                $readHeight = $row['tankHeight'];
			                $readLenght = $row['tankLenght'];
			                $readWidth = $row['tankWidth'];
			                $readTank = $row['tankType'];
			                if($readTank == 'A'){
			                    if($readWidth > 0 && $readHeight > 0 && $heightValue > 0){
			                        //Vertical Cylindrical
			                        $liquidHeight = $readHeight - $heightValue;
			                        $tankRaduis = $readWidth/2;
			                        $volumeValue = 3.1415926535897932384626433832795 * $tankRaduis * $tankRaduis * $liquidHeight;
			                        $volumeValue = round(($volumeValue/1000),2);
			                    }else{
			                        $volumeValue = 0;  
			                    }
			                    echo 'Height: '. $liquidHeight;
			                    echo 'Lenght: '. $readLenght;
			                    echo 'Width: '. $readWidth;
			                    echo 'Volume: '.$volumeValue;
			                }
			                if($readTank == 'B'){
			                    if($readWidth > 0 && $heightValue > 0 && $readLenght > 0){
			                        //Horizontal Cylindrical
			                        $tankRaduis = $readWidth/2;
			                        $a = (3.1415926535897932384626433832795 * $tankRaduis * $tankRaduis) - $tankRaduis * $tankRaduis * acos(($tankRaduis - $heightValue)/$tankRaduis);
			                        $b = ($tankRaduis - $heightValue) * sqrt((2 * $tankRaduis * $heightValue) - ($heightValue * $heightValue));
			                        $volumeValue = ($a + $b) * $readLenght;
			                        $volumeValue = round(($volumeValue/1000),2);
			                    }
			                    else{
			                        $volumeValue = 0;   
			                    }
			                    echo 'Height: '. $liquidHeight;
			                    echo 'Lenght: '. $readLenght;
			                    echo 'Width: '. $readWidth;
			                    echo 'Volume: '.$volumeValue;
			                }
			                if($readTank == 'C'){
			                    if($readHeight > 0 && $heightValue > 0 && $readLenght > 0 && $readWidth > 0){
			                        //Cubic
			                        $liquidHeight = $readHeight - $heightValue;
			                        $volumeValue = $readLenght * $readWidth * $liquidHeight;
			                        $volumeValue = round(($volumeValue/1000),2);
			                    }else{
			                        $volumeValue = 0; 
			                    }
			                    echo 'Height: '. $liquidHeight;
			                    echo 'Lenght: '. $readLenght;
			                    echo 'Width: '. $readWidth;
			                    echo 'Volume: '.$volumeValue;
			                }
			            }
				        // Fire SQL query to update data by id
				        $result3 = mysqli_query($connVar1, "UPDATE $tablename SET lastVoltage = $voltageValue, lastHeight = '$heightValue', lastDate = '$date', httpResponse = '$httpResponseValue', lastTemperature = '$temperatureValue' WHERE serial_no = '$serialValue'");
				        // Fire SQL query to insert data by id
				        $result4 = mysqli_query($connVar1, "INSERT INTO $serialValue(tankVolume,voltageValue,heightValue,timeValue,dateValue,httpResponse,temperatureValue) VALUES ('$volumeValue','$voltageValue','$heightValue','$time','$date','$httpResponseValue','$temperatureValue')");
			        }			
		        }
			}
		}
		else{
			//Client ID Does Not Exist
			//
			// Fire SQL query to insert data in exit table
			$result5 = mysqli_query($connVar1, "INSERT INTO $tablename(serial_no) VALUES('$serialValue')");
			// sql to create table
			$sql = "CREATE TABLE $serialValue (
			    tankVolume DOUBLE NOT NULL,
				heightValue DOUBLE NOT NULL,
				voltageValue DOUBLE NOT NULL,
				timeValue TIME NOT NULL,
				dateValue VARCHAR(255) NOT NULL,
				httpResponse VARCHAR(255) NOT NULL,
				temperatureValue DOUBLE NOT NULL
			)";

			if ($connVar1->query($sql) === TRUE) {
			    $result3 = mysqli_query($connVar1, "UPDATE $tablename SET lastVoltage = $voltageValue, lastHeight = '$heightValue', lastDate = '$date', httpResponse = '$httpResponseValue', lastTemperature = '$temperatureValue' WHERE serial_no = '$serialValue'");
			    if($result3){
			        $volumeValue = 0;
			        $result4 = mysqli_query($connVar1, "INSERT INTO $serialValue(tankVolume,voltageValue,heightValue,timeValue,dateValue,httpResponse,temperatureValue) VALUES ('$volumeValue','$voltageValue','$heightValue','$time','$date','$httpResponseValue','$temperatureValue')");
			        if($result4){
			            echo "Table $serialValue created successfully";
			        }
			    }
			} else {
				echo "Error creating table: " . $connVar1->error;
			}
		}
		$connVar1->close();
	}
	else{
        // sql to create table
	    $sql = "CREATE TABLE $tablename (
	        serial_no VARCHAR(20) NOT NULL,
	        branch VARCHAR(255) NOT NULL,
	        tankType VARCHAR(10) NOT NULL,
	        tankHeight DOUBLE NOT NULL,
	        tankLenght DOUBLE NOT NULL,
	        tankWidth DOUBLE NOT NULL,
	        lastHeight DOUBLE NOT NULL,
		    lastVoltage DOUBLE NOT NULL,
		    lastDate VARCHAR(255) NOT NULL,
		    latitudeValue DOUBLE NOT NULL,
		    longitudeValue DOUBLE NOT NULL,
		    httpResponse VARCHAR(255) NOT NULL,
		    lastTemperature DOUBLE NOT NULL
		)";
		//
		$connVar1->close();
    }
}

else {
    // If required parameter is missing
    $response["success"] = 0;
    $response["message"] = "Parameter(s) are missing. Please check the request";
 
    // Show JSON response
    echo json_encode($response);
}