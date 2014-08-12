<?php

	// Deinfe the include statments and global variables used for phpexcel:

	include_once "/Users/zhao0677/Projects/util-lib/commonFunc.php";

	$PHPEXCEL_PATH = "/Users/zhao0677/Projects/util-lib/PHPExcel_1.8.0/Classes/";

	/** PHPExcel */
	include_once $PHPEXCEL_PATH.'PHPExcel.php';

	/** PHPExcel_Writer_Excel2007 */
	include_once $PHPEXCEL_PATH.'PHPExcel/Writer/Excel2007.php';

	require_once $PHPEXCEL_PATH . 'PHPExcel/IOFactory.php';

	Abstract Class AbstractPHPExcel{

		static function getReader()
		{
			return PHPExcel_IOFactory::createReader('Excel2007');
		}

		static function loadFile($file)
		{
			return (AbstractPHPExcel::getReader()->load($file));
		}

		/* 
			1. so far this function only works with excel/csv file that has only one sheet.
			2. if this file contains a heading row, then it is supposed that all the columns have their heading,
			otherwise it reports an error.
		*/	
		static function getArrayDataFromExcel($file, $hasHeading = false)
		{
			$dataArray = Array();
			$headings = Array();

			$objPHPExcel = AbstractPHPExcel::loadFile($file);

			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				if($i == 0){
					$data = $worksheet->toArray();
					
					if($hasHeading == true){
						$headings = $data[0];
						unset($data[0]);
					}

					$rowCount = $hasHeading == true ? 1 : 0;
					$colCount = 0;
					foreach ($data as $key => $entry) {
						if(isset($entry) && count($entry) > 0 && !($entry[0] == "" && $entry[1] == "" && $entry[2] == "" && $entry[4] == "")){
							foreach ($headings as $col) {
								if(isset($col) && $col != ""){
									$dataArray[$rowCount][$col] = $entry[$colCount];
								}
								$colCount++;
							}
							$rowCount++;
							$colCount = 0;
						}
					}
				}
			}

			return $dataArray;
		}

		function validateXmlAgainstDtd($xmlFile, $dtd)
		{

			$root = 'foo';

			$old = new DOMDocument;
			$old->loadXML($xmlFile);

			$creator = new DOMImplementation;
			$doctype = $creator->createDocumentType($root, null, $dtd);
			$new = $creator->createDocument(null, null, $doctype);
			$new->encoding = "utf-8";

			$oldNode = $old->getElementsByTagName($root)->item(0);
			$newNode = $new->importNode($oldNode, true);
			$new->appendChild($newNode);

			return $new->validate();
		}

		abstract function getXmlFromExcel();
		abstract function getXmlFromArray();
		abstract function printExcelData();
		abstract function cleanData();

	}

