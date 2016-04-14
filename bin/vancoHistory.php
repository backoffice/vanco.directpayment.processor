<?php

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';

class bin_vancoHistory {
    function __construct( ) {
        require_once 'CRM/Utils/System.php';
        require_once 'CRM/Utils/System.php';
        require_once 'CRM/Contribute/BAO/Contribution.php';
        require_once 'CRM/Core/BAO/UFMatch.php';
		require_once "CRM/Core/BAO/Domain.php";
		$config = CRM_Core_Config::singleton();
        $domainValues = CRM_Core_BAO_Domain::getNameAndEmail( );
        $this->_from  = "$domainValues[0] <$domainValues[1]>";


		$customExt = $config->extensionsDir;			
		$customExt = rtrim( $customExt,"/");
		$this->_customExt = $customExt;
		require_once "$customExt/vanco.directpayment.processor/vanco.php";
					
        if( !$this->_date ) {
            $this->_date = date('Y-m-d');
        }
        //get raw xml
        $xml = file_get_contents('php://input');   
		//Sample POST
		//$xml =  "<VancoWS><Auth><PostType>EFTTransactionActivity</PostType><PostTime>2016-04-04 17:03:25</PostTime><Version>3</Version></Auth><ClientID>ES19804</ClientID><EventCount>1</EventCount><Events><Event><EventType>ACHProcessed</EventType><CustomerRef>14012579</CustomerRef><PaymentMethodRef>111111</PaymentMethodRef><TransactionRef>111111</TransactionRef><Frequencycode>M</Frequencycode><ProcessDate>2016-04-04</ProcessDate><SettlementDate>2016-04-05</SettlementDate><DepositDate>2016-04-05</DepositDate><Amount>25.00</Amount><Credit>D</Credit></Event></Events></VancoWS>";
		
		$this->log('Notification', $xml);
        $trxnData = $this->parseXML( $xml );        
        if ( $trxnData ) {			
            $paymentHistory = $this->transactionhistory( $trxnData );
        }
 
    }

	function log($type,$xml,$fileName = null) {
        require_once 'CRM/Core/DAO.php';
        $fileName  = $this->_customExt."/vanco.directpayment.processor/log/trxn_log_";
        
        $xmlObject = simplexml_load_string( $xml );
        
		if($xml)
            {
                $file = fopen( $fileName.''.date('Ymd').'.xml', 'a' );
                fwrite($file,"-----------------$type START--------------\r\n");
                fwrite($file, "$type Time: ".date('d-m-y h:i:s')."\r\n");
                fwrite($file,"$type: ".$xml."\r\n");
                fwrite($file,"-----------------$type END--------------\r\n");
                fclose($file);
            } else {
				$file = fopen( $fileName.''.date('Ymd').'.xml', 'a' );
   
                fwrite($file,"$type: ".$xml."\r\n");
                fclose($file);			
			}
	}
	
    function transactionhistory( $trxnData ){
        require_once "CRM/Financial/BAO/PaymentProcessor.php";
        require_once "CRM/Pledge/BAO/Pledge.php";
        require_once 'CRM/Contribute/BAO/Contribution.php';
        require_once 'CRM/Core/DAO.php';

        require_once 'CRM/Core/BAO/CustomGroup.php';
		$successfulTrxns = 0;
		$failedTrxns = 0;
		$info = '';
		
        foreach ( $trxnData as $key => $value ) {
		
			if ( $key === 'clientid' ) {			
				continue;
			}

			$flag = 0;
			if ( $value['EVENTTYPE'] == 'ACHReturn' ) {
				$status = 4;
				$flag   = 1;
			} else if ( $value['EVENTTYPE'] == 'ACHProcessed' || $value['EVENTTYPE'] == 'CCSuccess'  ) {
				$status = 1;
			} else if ( $value['EVENTTYPE'] == 'CCFailure' ) {
				$status = 4;				
			} else if ( $value['EVENTTYPE'] == 'CCChargeback' ) {
			
			}
			
			$trxnRef = $value['TRANSACTIONREF'];
					
			//get data from contribution recur table
			
			$select = array('id', 'contact_id', 'amount', 'frequency_unit', 'frequency_interval', 'installments', 'start_date', 'create_date', 'modified_date', 'cancel_date',  'end_date', 'processor_id', 'trxn_id',  'invoice_id', 'contribution_status_id', 'is_test', 'cycle_day', 'failure_count', 'failure_retry_date','auto_renew', 'currency', 'payment_processor_id');

			$config = CRM_Core_Config::singleton();
			$customExt = $config->extensionsDir;
			
			$customExt = rtrim( $customExt,"/");
			
			require_once "$customExt/vanco.directpayment.processor/vanco.php";
			$data = $this->getRecurPaymentDetails( $select, array( 'trxn_id' => $trxnRef ));

			if( is_array( $data ) ) {							
				//******************
				$contributionParams = array( 'contribution_recur_id' => $data[0]['id'] );
				$totalInstallments = $data[0]['installments'];
				$values = array( );
				$ids = array( );
				$ContributionDetails = $this->getPaymentDetails( NULL, $contributionParams );

				$installmentCount = $ContributionDetails[0]->N;
				$contact_id       = $ContributionDetails[0]->contact_id;                        
				$totalAmount      = $ContributionDetails[0]->total_amount;
				
				
				//get payment instrument id
				$ACHid = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 'ACH', 'value', 'name' );
				
				$CCid = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 'Credit Card', 'value', 'name' );	
				 if ( !$ContributionDetails[ $installmentCount - 1 ]->payment_instrument_id ) {
					if ( strpos( $value['EVENTTYPE'], 'ACH') === 0 ) {
						$payment_instrument = 'ACH';
						$payment_instrument_id = $ACHid;
					} else {
						$payment_instrument = 'Credit Card';
						$payment_instrument_id = $CCid;
					}
				 } else {
					$paymentMethods = CRM_Contribute_PseudoConstant::paymentInstrument();
					$payment_instrument_id = $ContributionDetails[ $installmentCount - 1 ]->payment_instrument_id;
					$payment_instrument = $paymentMethods[$payment_instrument_id];
				 }
		
				if ( $installmentCount == 1 && $ContributionDetails[ $installmentCount - 1 ]->contribution_status_id == 2 ) {
					
					
					$ids['contribution'] = $ContributionDetails[ $installmentCount - 1 ]->id;
					$contributionPrms  = array( 'id'                      => $ContributionDetails[ $installmentCount - 1 ]->id,
											    'contact_id'              => $ContributionDetails[ $installmentCount - 1 ]->contact_id,
											    'contribution_status_id'  => $status);
					
					$ids = array('contribution' => $ContributionDetails[ $installmentCount - 1 ]->id);	

					$updatecontribution = civicrm_api3('contribution', 'create', $contributionPrms);
					if ( !$updatecontribution['is_error'] ) {
						$info .= "Object - Recurring Contribution, Id - ".$updatecontribution['id'].", Action - Updated \n ";
						$successfulTrxns++;
						
						// Obtaining contribution obj of the updated contribution record

						$newContributionDetails       = $updatecontribution['values'][$updatecontribution['id']];
						$newContributionDetails['id'] = $updatecontribution['id'];
						
						if( !$newContributionDetails['is_error'] ) {
							$newContributionDetails['id'] = $newContributionDetails['contribution_id'];			
							//Send notification to user for new record
							require_once "CRM/Contribute/DAO/ContributionRecur.php";
							$recur = new CRM_Contribute_DAO_ContributionRecur();
							$recur->id = $newContributionDetails['contribution_recur_id'];
							$recur->find(true);     
							$contactid = $newContributionDetails['contact_id'];
							$contribution_page_id = $newContributionDetails['contribution_page_id'];
							
							//Send Notification
							require_once 'CRM/Contribute/BAO/ContributionPage.php';
							$subscriptionPaymentStatus = 'START';
							
							$this->recurringNotify( $subscriptionPaymentStatus, $contactid,
																				  $contribution_page_id, $recur );
							//*************				
							$contriArr[$newContributionDetails->id][] = 'new';
												
						}
					}				
						
				} else if ( $status == 4 ) {			
					$info .= "Received failed transaction with no contribution record. Trxn Ref - $trxnRef \n ";
					$failedTrxns++;
				} else { //Adding a new record in Contribution table if no pending record exists
					$ids = array();
					//Calculation of date for Receive date in Contribution Table
					require_once 'CRM/Utils/Date.php';
					$timestamp  = date("H:i:s");
					$nextDate  = date("Y-m-d", strtotime(  $value['SETTLEMENTDATE'] ) );
					$date = explode('-',$nextDate );
					$time =  explode(':', $timestamp);
					$trxn_date = CRM_Utils_Date::format(array('Y'=>$date[0], 'M'=>$date[1], 'd'=>$date[2], 'H'=>$time[0], 'i'=>$time[1], 's'=>$time[2] ) );
	
					$contributionParams = array( 'contact_id'             => $ContributionDetails[ $installmentCount - 1 ]->contact_id,
												 'financial_type_id'   => $ContributionDetails[ $installmentCount - 1 ]->financial_type_id,
												 'contribution_page_id'   => $ContributionDetails[ $installmentCount - 1 ]->contribution_page_id,
												 'payment_instrument'     => $payment_instrument,
												 'total_amount'           => $ContributionDetails[ $installmentCount - 1 ]->total_amount,
												 'trxn_id'                => $trxnRef.'-'.$installmentCount,
												 'invoice_id'             => $trxnRef.'-'.$installmentCount,
												 'currency'               => $ContributionDetails[ $installmentCount - 1 ]->currency,
												 'receive_date'           => $trxn_date,
												 'source'                 => $ContributionDetails[ $installmentCount - 1 ]->source,
												 'amount_level'           => $ContributionDetails[ $installmentCount - 1 ]->amount_level,
												 'contribution_recur_id'  => $ContributionDetails[ $installmentCount - 1 ]->contribution_recur_id,
												 'honor_contact_id'       => $ContributionDetails[ $installmentCount - 1 ]->honor_contact_id,
												 'is_test'                => $ContributionDetails[ $installmentCount - 1 ]->is_test,
												 'is_pay_later'           => $ContributionDetails[ $installmentCount - 1 ]->is_pay_later,
												 'contribution_status_id' => $status,
												 'honor_type_id'          => $ContributionDetails[ $installmentCount - 1 ]->honor_type_id,
												 'address_id'             => $ContributionDetails[ $installmentCount - 1 ]->address_id,
												 'check_number'           => $ContributionDetails[ $installmentCount - 1 ]->check_number
												 );					
					
					$newcontribution = civicrm_api3('contribution', 'create', $contributionParams);
				
					//Add an entry to Financial transaction table
					//for successful transaction
					if ( !$newcontribution['is_error'] ) {	
						$newContributionDetails = $newcontribution['values'][$newcontribution['id']];
						$info  .= "Object - Recurring Contribution, Id - ".$newContributionDetails['id'].", Action - Created  \n";
						$successfulTrxns++;	
					
						if ( $status == 1 && $newContributionDetails ) {
							$id = CRM_Core_DAO::getFieldvalue( 'CRM_Financial_DAO_FinancialTrxn', $newContributionDetails['trxn_id'], 'id', 'trxn_id');
							if ( !$id ) {
								$this->createFinancialTrxn( $newContributionDetails );
							}
						}
						$installmentCount++;
					}
					
				} 
				if ( !$newContributionDetails['is_error'] ) {
					//Update the status and end date of the recur contribution is its the last installment
					if ( $installmentCount >= $totalInstallments ) {
						$contributionRecurParams = array( 'id'                     => $data[0]['id'],
														  'contribution_status_id' => 1,
													      'end_date'               => $trxn_date
														  );
						$id = array ( 'contribution' => $data[0]['id'] );
						require_once 'CRM/Contribute/BAO/ContributionRecur.php'; 
						$test = CRM_Contribute_BAO_ContributionRecur::add($contributionRecurParams, $id);
					}
				//***************
				}
			} else {
				//$contactid = $value['CUSTOMERREF'];
				$contributionParams = array( 'trxn_id'    => $trxnRef,
											 );
				$values = array(); 
				$ids = array( );
				switch ( $value['EVENTTYPE'] ) {
				case "CCSuccess":
					$status = 1;
					break;
				case "CCFailure":
					$status = 4;
					break;
				}

				$ContributionDetails = $this->getPaymentDetails( NULL, $contributionParams );
				if ( $ContributionDetails ) {
					$installmentCount = $ContributionDetails[0]->N;
					$contribution_page_id = $ContributionDetails[0]->contribution_page_id;
					$contribution_recur_id = $ContributionDetails[0]->contribution_recur_id;

					if( $ContributionDetails[ $installmentCount - 1 ]->id ){
						$contributionParams = array( 'id'                     => $ContributionDetails[ $installmentCount - 1 ]->id,
													 'contact_id'             => $ContributionDetails[ $installmentCount - 1 ]->contact_id,
													 'contribution_status_id' => $status);

						$currentDate = date('Y-m-d');

						if ( $status == 1 && $value['SETTLEMENTDATE'] < $currentDate ) {
							$sdate    = $value['SETTLEMENTDATE'];							
							$contributionParams['receive_date'] = $value['SETTLEMENTDATE'];
							
						}

						$ids['contribution'] = $contributionParams['id'];
						
						$updatecontribution = civicrm_api3('contribution', 'create', $contributionParams);
						$newContributionDetails = $updatecontribution['values'][$updatecontribution['id']];
						
						if ( $newContributionDetails ) {
							$info  .= "Object - One time Contribution, Id - ".$newContributionDetails['id'].", Action - Updated \n";
							$successfulTrxns++;		
	
						}
						$contriArr[$ContributionDetails[ $installmentCount - 1 ]->id][] = 'one time update';
					}
				} else {
					// If transaction Reference is not in any
					// table					
					$failedTrxns++;	
					$xml = new SimpleXMLElement('<error/>');
					$value = array_flip($value);
					array_walk_recursive( $value, array($xml, 'addChild'));
					$this->log('Failed Transactions Log', $xml->asXML() );
				}
			}
		}

//--------------------END - TRANSACTION

		$this->log('Processing Log', $info);
		$logStats = "Successful Transactions - ".$successfulTrxns." \nFailed Transactions - ".$failedTrxns;

		$this->log('Statistics', $logStats);

        return $result;
    }
    
 function createFinancialTrxn( $contribution ) {
        require_once 'CRM/Core/DAO.php';
        $date      = explode('-', $contribution->receive_date);
        $datetemp  = explode(' ', $date[2]);
        $date[2]   = $datetemp[0];
        $trxn_date = CRM_Utils_Date::format(array('Y'=>$date[0], 'M'=>$date[1], 'd'=>$date[2]));
        if ( $trxn_date ) {
            $receive_date = $trxn_date;
        } else {
            $receive_date = $contribution->receive_date;
        }
        if ( $contribution->net_amount ) {
            $net_amount = $contribution->net_amount;
        } else { 
            $net_amount = '0.00';
        }
        $paymentProcessor = CRM_Core_DAO::getFieldValue( 'CRM_Financial_DAO_PaymentProcessor', 'Vanco', 'name', 'payment_processor_type');
        $trxnParams = array(
                            'contribution_id'   => $contribution['id'],
                            'trxn_date'         => $receive_date,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $contribution['total_amount'],
                            'fee_amount'        => $contribution['fee_amount'],
                            'net_amount'        => $net_amount,
                            'currency'          => $contribution['currency'],
                            'payment_processor' => $paymentProcessor,
                            'trxn_id'           => $contribution['trxn_id'],
                            );
        require_once 'CRM/Financial/BAO/FinancialTrxn.php';
		
		$trxn =& CRM_Financial_BAO_FinancialTrxn::create( $trxnParams );
    }

 function parseXML( $xml ) {
        //invalid xml file
        $xmlparser = xml_parser_create();
        $parse = xml_parse_into_struct($xmlparser,$xml,$values, $indexes);
        xml_parser_free($xmlparser);    

		$requiredData = array( 'EVENTTYPE', 'CUSTOMERREF', 'PAYMENTMETHODREF', 'TRANSACTIONREF', 'FREQUENCYCODE', 'PROCESSDATE', 'SETTLEMENTDATE', 'DEPOSITDATE', 'CCAUTHDESC', 'AMOUNT', 'CREDIT', 'RETURNDATE', 'RETURNCODE', 'RETURNREASON');
        $trxndata = array( );
        if ( $parse ) {
            foreach( $indexes as $key => $value ) {	
			
                if ( in_array( $key, $requiredData ) ) {
					foreach ( $value as $k => $seq ) {
                        $trxndata[$k][$key] = $values[$seq]['value'];
                    }
                }
				if ( $key == 'CLIENTID' ){
                    $trxndata['clientid'] = $values[$value[0]]['value'];
                }               
            }
            return( $trxndata );
        } else {
            return false;
        }
    }
	
	function getRecurPaymentDetails( $recurSelectParam, $recurWhereParam ) { 
		$selectParams = implode( ',', $recurSelectParam );
		$whereParams = "";
		foreach( $recurWhereParam as $whereKey => $whereValue ) {
			$whereParamsArray[] = $whereKey . " = '" . $whereValue ."'";
		}
		$whereParams = implode( ' AND ', $whereParamsArray );
		$sql = "SELECT " . $selectParams ." FROM civicrm_contribution_recur where " . $whereParams .";" ;
		$recurDetails =& CRM_Core_DAO::executeQuery( $sql );
		$index = 0;
		while( $recurDetails->fetch() ){
			foreach( $recurSelectParam as $selectKey ) {
				$details[$index][$selectKey] = $recurDetails->$selectKey;
			}
			$index++;
		}
		return $details;
	}

	function getPaymentDetails( $SelectParam, $WhereParam, $like = FALSE ) { 
		if( $SelectParam ){
			$selectParams = implode( ',', $SelectParam );
		} else {
			$selectParams = '*';
		}
		$whereParams = "";
		if( $like ){
			$operator = ' like ';
		} else {
			$operator = ' = ';
		}
		foreach( $WhereParam as $whereKey => $whereValue ) {
			$whereParamsArray[] = $whereKey . $operator."'" . $whereValue ."'";
		}
		$whereParams = implode( ' AND ', $whereParamsArray );
		$sql = "SELECT DISTINCT " . $selectParams ." FROM civicrm_contribution where " . $whereParams .";" ;
		$Details =& CRM_Core_DAO::executeQuery( $sql );
		$index = 0;
		$details = array();
		while( $Details->fetch() ){
			if( $SelectParam ){
				foreach( $SelectParam as $selectKey ) {
					$details[$index][$selectKey] = $Details->$selectKey;
				}
			} else {
				$details[$index] = clone $Details;
			}
			$index++;
		}
		return $details;
	}  
	
	
  /**
   * Function to send the emails for Recurring Contribution Notication
   *
   * @param string  $type         txnType
   * @param int     $contactID    contact id for contributor
   * @param int     $pageID       contribution page id
   * @param object  $recur        object of recurring contribution table
   * @param object  $autoRenewMembership   is it a auto renew membership.
   *
   * @return void
   * @access public
   * @static
   */
  static function recurringNotify($type, $contactID, $pageID, $recur, $autoRenewMembership = FALSE) {
    $value = array();
    if ($pageID) {
      CRM_Core_DAO::commonRetrieveAll('CRM_Contribute_DAO_ContributionPage', 'id', $pageID, $value, array(
          'title',
          'is_email_receipt',
          'receipt_from_name',
          'receipt_from_email',
          'cc_receipt',
          'bcc_receipt',
        ));
    }

    $isEmailReceipt = CRM_Utils_Array::value('is_email_receipt', $value[$pageID]);
    $isOfflineRecur = FALSE;
    if (!$pageID && $recur->id) {
      $isOfflineRecur = TRUE;
    }
    if ($isEmailReceipt || $isOfflineRecur) {
      if ($pageID) {
        $receiptFrom = '"' . CRM_Utils_Array::value('receipt_from_name', $value[$pageID]) . '" <' . $value[$pageID]['receipt_from_email'] . '>';

        $receiptFromName = $value[$pageID]['receipt_from_name'];
        $receiptFromEmail = $value[$pageID]['receipt_from_email'];
      }
      else {
        $domainValues     = CRM_Core_BAO_Domain::getNameAndEmail();
        $receiptFrom      = "$domainValues[0] <$domainValues[1]>";
        $receiptFromName  = $domainValues[0];
        $receiptFromEmail = $domainValues[1];
      }
	   $contributionType = CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_FinancialType', $recur->financial_type_id, 'name');
	   $address  = CRM_Core_BAO_Address::allAddress( $contactID );
	   foreach( $address as $k => $v ) {
			$buildAddress = '';
			//get full address info for address ids
			$params        = array( 'id' => $v );
			$fullAddress   = CRM_Core_BAO_Address::getValues( $params, FALSE, 'id' );
			//Get location type name
			$locationType  = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_LocationType', $k, 'name', 'id' );
			//Get state province name
			$stateProvince = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_StateProvince', $fullAddress[1]['state_province_id'], 'name', 'id' );
			//get country name
			$country       = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Country', $fullAddress[1]['country_id'], 'name', 'id' );
			//Build complete address HTML
			if ( $fullAddress[1]['name'] ) {
				$buildAddress = $fullAddress[1]['name']."<br/>";
			}
			if ( $fullAddress[1]['street_address'] ) {
				$buildAddress .= $fullAddress[1]['street_address']."<br/>";
			}
			if ( $fullAddress[1]['supplemental_address_1'] ) {
				$buildAddress .= $fullAddress[1]['supplemental_address_1']."<br/>";
			}
			if ( $fullAddress[1]['supplemental_address_2'] ) {
				$buildAddress .= $fullAddress[1]['supplemental_address_2']."<br/>";
			}
			if ( $stateProvince ) {
				$buildAddress .= $stateProvince." ";
			}
			if ( $fullAddress[1]['postal_code'] ) {
				$buildAddress .= $fullAddress[1]['postal_code']."<br/>";
			}
			if ( $country ) {
				$buildAddress .= $country;
			}		
	  }
	  
	  require_once "CRM/Core/BAO/Email.php";
	  $emailInfo = CRM_Core_BAO_Email::allEmails($contactID);
	
	  foreach( $emailInfo as $val ) {
		if ( $val['locationType'] == 'Billing' ) {
			$email = $val['email'];
		}
	  }
	 
	  require_once "CRM/Core/BAO/Phone.php";
	  $phoneInfo = CRM_Core_BAO_Phone::allPhones($contactID);
	  foreach( $phoneInfo as $val ) {
		if ( $val['locationType'] == 'Billing' ) {
			$phone = $val['phone'];
		}
	  }

      list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($contactID, FALSE);
      $templatesParams = array(
        'groupName' => 'msg_tpl_workflow_contribution',
        'valueName' => 'contribution_recurring_notify',
        'contactId' => $contactID,
        'tplParams' => array(
          'recur_frequency_interval' => $recur->frequency_interval,
          'recur_frequency_unit' => $recur->frequency_unit,
          'recur_installments' => $recur->installments,
          'recur_start_date' => $recur->start_date,
          'recur_end_date' => $recur->end_date,
          'recur_amount' => $recur->amount,
          'recur_txnType' => $type,
          'displayName' => $displayName,
          'receipt_from_name' => $receiptFromName,
          'receipt_from_email' => $receiptFromEmail,
          'auto_renew_membership' => $autoRenewMembership,
		  'recur_trxn_id' => $recur->trxn_id,
		  'contributionTypeName' => $contributionType,
		  'address' => $buildAddress,
		  'email' => $email,
		  'phone' => $phone
        ),
        'from' => $receiptFrom,
        'toName' => $displayName,
        'toEmail' => $email,
      );

      if ($recur->id) {
        // in some cases its just recurringNotify() thats called for the first time and these urls don't get set.
        // like in PaypalPro, & therefore we set it here additionally.
        $template         = CRM_Core_Smarty::singleton();
        $paymentProcessor = CRM_Financial_BAO_PaymentProcessor::getProcessorForEntity($recur->id, 'recur', 'obj');
        $url              = $paymentProcessor->subscriptionURL($recur->id, 'recur');
        $template->assign('cancelSubscriptionUrl', $url);

        $url = $paymentProcessor->subscriptionURL($recur->id, 'recur', 'billing');
        $template->assign('updateSubscriptionBillingUrl', $url);

        $url = $paymentProcessor->subscriptionURL($recur->id, 'recur', 'update');
        $template->assign('updateSubscriptionUrl', $url);
      }

      list($sent, $subject, $message, $html) = CRM_Core_BAO_MessageTemplate::sendTemplate($templatesParams);

      if ($sent) {
        CRM_Core_Error::debug_log_message('Success: mail sent for recurring notification.');
      }
      else {
        CRM_Core_Error::debug_log_message('Failure: mail not sent for recurring notification.');
      }
    }
  }

}

$vancoHistory = new bin_vancoHistory();

