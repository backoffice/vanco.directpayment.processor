<?php

require_once 'vanco.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function vanco_civicrm_config(&$config) {
  _vanco_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function vanco_civicrm_xmlMenu(&$files) {
  _vanco_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function vanco_civicrm_install() {
  _vanco_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function vanco_civicrm_uninstall() {
  _vanco_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function vanco_civicrm_enable() {
  _vanco_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function vanco_civicrm_disable() {
  _vanco_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function vanco_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _vanco_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function vanco_civicrm_managed(&$entities) {
  $entities[] = array(
    'module' => 'vanco.directpayment.processor',
    'name' => 'Vanco Payments Credit Card',
    'entity' => 'PaymentProcessorType',
    'params' => array(
      'version' => 3,
      'name' => 'Vanco Payments Credit Card',
      'title' => 'Vanco Payments Credit Card',
      'description' => 'Vanco Credit Card payment Processor',
      'class_name' => 'Payment_VancoCreditCard',
      'billing_mode' => 'form',
      'user_name_label' => 'User Id',
      'password_label' => 'Password',
      'url_site_default' => 'https://myvanco.vancopayments.com',
      'url_api_default'  => 'https://myvanco.vancopayments.com/cgi-bin/ws2.vps',
      'url_site_test_default' => 'https://www.vancodev.com',
      'url_api_test_default'  => 'https://www.vancodev.com/cgi-bin/wstest2.vps',
      'is_recur' => 1,
      'payment_type' => 1,
    ),
  );
  $entities[] = array(
    'module' => 'vanco.directpayment.processor',
    'name' => 'Vanco Payments ACH',
    'entity' => 'PaymentProcessorType',
    'params' => array(
      'version' => 3,
      'name' => 'Vanco Payments ACH',
      'title' => 'Vanco Payments ACH',
      'description' => 'Vanco ACH Payment Processor',
      'class_name' => 'Payment_VancoACH',
      'billing_mode' => 'form',
      'user_name_label' => 'User Id',
      'password_label' => 'Password',
      'url_site_default' => 'https://myvanco.vancopayments.com',
      'url_api_default'  => 'https://myvanco.vancopayments.com/cgi-bin/ws2.vps',
      'url_site_test_default' => 'https://www.vancodev.com',
      'url_api_test_default'  => 'https://www.vancodev.com/cgi-bin/wstest2.vps',
      'is_recur' => 1,
      'payment_type' => 1,
    ),
  );
  _vanco_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function vanco_civicrm_caseTypes(&$caseTypes) {
  _vanco_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function vanco_civicrm_angularModules(&$angularModules) {
_vanco_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function vanco_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _vanco_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function vanco_civicrm_preProcess($formName, &$form) {

}

*/

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_pre
 * */

function vanco_civicrm_pre($op, $objectName, $objectId, &$params) {

  if (($op == 'create') && ($objectName == 'Contribution') && !empty($params['contribution_status_id'])) {
    $smarty =  CRM_Core_Smarty::singleton( );
    $values = $smarty->get_template_vars( );
    $paymentClass = $values['paymentProcessor']['class_name'];
		if ( !empty($values['paymentProcessor']) && $paymentClass == 'Payment_VancoACH' || $paymentClass == 'Payment_VancoCreditCard') {
      if( $paymentClass == 'Payment_VancoACH' ) {
        $paymentInstrument = CRM_Contribute_PseudoConstant::paymentInstrument();
        //Setting vanco payment instrument id for ACH payment
		//ACH_INSTRT_NAME - set in vanco.directpayment.processor/CRM/Core/Payment/vanco_settings.inc
        if( $instdID =  array_search(ACH_INSTRT_NAME, $paymentInstrument )) {
          $params['payment_instrument_id'] = $instdID;
        }
      }
    }
  }
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 * */

function vanco_civicrm_buildForm($formName, &$form) {

  $mode = "backend";

  // but start by grouping a few forms together for nicer code
  switch ($formName) {

    case 'CRM_Contribute_Form_Contribution_Main':
    case 'CRM_Event_Form_Registration_Register':
    case 'CRM_Contribute_Form_Contribution':
    case 'CRM_Event_Form_Participant':
    case 'CRM_Member_Form_Membership':
      // override normal convention, deal with all these front-end contribution forms the same way
      if($formName == 'CRM_Contribute_Form_Contribution_Main' || $formName == 'CRM_Event_Form_Registration_Register') {
        $mode = "transact";
      }
      $fname = 'vanco_civicrm_buildForm_Contribution_Frontend';
      break;

    case 'CRM_Contribute_Form_Contribution_Confirm':
    case 'CRM_Event_Form_Registration_Confirm':
      // on the confirmation form, we know the processor, so only do processor specific customizations
      if($formName == 'CRM_Event_Form_Registration_Confirm') {
        $mode = "event-confirm";
      }
        $fname = 'vanco_civicrm_buildForm_Confirm_'.$form->_paymentProcessor['class_name'];
      break;

    case 'CRM_Contribute_Form_Contribution_ThankYou':
    case 'CRM_Event_Form_Registration_ThankYou':
      // on the confirmation form, we know the processor, so only do processor specific customizations
      if($formName = 'CRM_Event_Form_Registration_ThankYou') {
        $mode = "event-thankyou";
      }
        $fname = 'vanco_civicrm_buildForm_ThankYou_'.$form->_paymentProcessor['class_name'];
      break;

    default:
      $fname = 'vanco_civicrm_buildForm_'.$formName;
      break;
  }
  if (isset($fname) && function_exists($fname)) {
    $fname($form, $mode);
  }
  // else echo $fname;
}

/*
 * Displaying payment detail on confirm page for 'Vanco ACH Payment Option'
 */

function vanco_civicrm_buildForm_Confirm_Payment_vancoACH(&$form, $mode) {
  $form->add( 'text', 'routing_number', ts('Routing Number'));
  $form->add( 'text', 'account_number', ts('Account Number'));
  $form->add( 'select', 'account_type', ts( 'Account Type' ), array('checking' => ts('Checking'),
                                                                    'savings'  => ts('Savings') ));

  $params = $form->getVar('_params');
  if(isset($params[0])) {
    $params = $params[0];
  }

  foreach(array('routing_number','account_number','account_type') as $k) {
    $defaults[$k] = $params[$k];
  };

  $form->setDefaults($defaults);

  $Regioninstance = "contribution-confirm-billing-block";

  if($mode == 'event-confirm') {
    $Regioninstance = "page-header";
    $form->assign('vancoRegionEvent', TRUE);
  }

  CRM_Core_Region::instance($Regioninstance)->add(array(
      'template' => 'CRM/Vanco/ConfirmExtra_ACH.tpl'
    ));
}

/*
 * Displaying payment detail on thank you page for 'Vanco ACH Payment Option'
 */

function vanco_civicrm_buildForm_ThankYou_Payment_vancoACH(&$form, $mode) {

  $params = $form->getVar('_params');
  if(isset($params[0])) {
    $params = $params[0];
  }

  foreach(array('routing_number','account_number','account_type') as $k) {
    $form->addElement('static', $k, $k, $params[$k]);
  };

  $Regioninstance = "contribution-thankyou-billing-block";

  if($mode == 'event-thankyou') {
    $Regioninstance = "page-header";
  }

  CRM_Core_Region::instance($Regioninstance)->add(array(
      'template' => 'CRM/Vanco/ThankYouExtra_ACH.tpl'
    ));
}


/*
 * Helper function for dispalying frontend form for contribution and event form
 */

function vanco_civicrm_buildForm_Contribution_Frontend(&$form, $mode) {

    $PaymentProcessors = $form->getVar('_paymentProcessors');
    if (empty($PaymentProcessors)) {
      return;
    }

   $session = CRM_Core_Session::singleton();
   $session->set("Vanco_ProcessorResult", NULL);

   //Adding 'biweek' and 'quarter' frequency units for vanco ACH payment TODO : handling frequecy units on selection of payment processor
   if((isset($form->_values['is_recur']) || $mode != 'transact') && $form->elementExists('frequency_unit')) {
     $frequecy_units = $form->getElement('frequency_unit');
     if($frequecy_units->_type == 'select') {
       $frequecy_units->addOption('biweek','biweek', array('class' => 'vanco-frequecy-units'));
       $frequecy_units->addOption('quarter','quarter',array('class' => 'vanco-frequecy-units'));
     }
   }

 if( isset($form->_values['custom_pre_id']) && $form->_values['custom_pre_id'] == 1 ) {
   $form->add( 'checkbox', 'copy_home_address', 'Billing same as above', null, null, array('onClick' => 'copyAddress();') );
 }

  //Getting list of all payment processors used for ACH payment type
  $list = vanco_civicrm_processors($PaymentProcessors, 'Payment_vancoACH');

  /* Checking the payament processor available on form  */
  if (!empty($list) && !empty($PaymentProcessors) && !empty($form->_paymentProcessor) && (!empty($list[$form->_paymentProcessor['id']]) || $mode != 'transact')) {

    if($mode == 'transact') {
      // we need to remove them from the _paymentFields as well or they'll sneak back in!
      $form->removeElement('credit_card_type',TRUE);
      $form->removeElement('credit_card_number',TRUE);
      $form->removeElement('credit_card_exp_date',TRUE);
      $form->removeElement('cvv2',TRUE);
      unset($form->_paymentFields['credit_card_type']);
      unset($form->_paymentFields['credit_card_number']);
      unset($form->_paymentFields['credit_card_exp_date']);
      unset($form->_paymentFields['cvv2']);
    }
    $form->add( 'text', 'routing_number', ts('Routing Number'), array('size' => 15, 'maxlength' => 10, 'autocomplete' => 'off'), TRUE);
    $form->add( 'text', 'account_number', ts('Account Number'), array('size' => 15, 'autocomplete' => 'off'), TRUE);
    $form->add( 'select', 'account_type', ts('Account Type'), array('checking' => ts('Checking'),
                                                                      'savings' => ts('Savings') ), TRUE );

    //Assigning value for backened contribution to differentiate between the "Live Contribution page" and "Backend Contribution page"
    if($mode != 'transact') {
      $form->assign("BackendContribution", TRUE);
      $PPIDs = array_keys($list);
      $form->assign("ACHProcessors", json_encode(array_combine($PPIDs, $PPIDs)));
      $form->_params["VancoACHProcessors"] = $PPIDs;
    }

    CRM_Core_Region::instance('billing-block')->add(array(
        'template' => 'CRM/Vanco/BillingBlockACHExtra.tpl'
      ));
  }
}


/*
 * Utility function to see if a payment processor id is using one of the Vanco payment processors
 *
 * This function relies on our naming convention for the Vanco payment processor classes, staring with the string Payment_vancoACH
 */

function _vanco_civicrm_get_type($payment_processor_id) {
  $params = array(
    'version' => 3,
    'sequential' => 1,
    'id' => $payment_processor_id,
  );
  $result = civicrm_api('PaymentProcessor', 'getsingle', $params);
  if (empty($result['class_name'])) {
    return FALSE;
    // TODO: log error
  }
  $type = $result['class_name'];
  return $type;
}

/**
 * Implements hook_civicrm_tokens().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tokens
 * */

function vanco_civicrm_tokens( &$tokens ) {
    $tokens['contribution'] = array( '{contribution.errorLog}' => 'Vanco error log');
}

/**
 * Implements hook_civicrm_tokenValues().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tokenValues
 * */

function vanco_civicrm_tokenValues( &$values, &$contactIDs ) {
    require_once 'CRM/Core/DAO.php';
    $customExt = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 'Custom Extensions', 'value', 'label' );

    $myFile = $customExt.'/vanco.directpayment.processor/CRM/Vanco/log/vanco_error_log_'.date('Ymd').'.xml';
    if( file_exists( $myFile ) ){
        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        if( is_array( $contactIDs ) ){
            $values[$contactIDs[0]]['contribution.errorLog'] = $theData;
        } else {
            $values['contribution.errorLog'] = $theData;
        }
    }
}


/*
 * Helper function to get all the payment processor added in civicrm with repect payment processor class name
 */

function vanco_civicrm_processors($processors, $class_name, $params = array()) {
  $list = array();
  $params = $params + array('version' => 3, 'sequential' => 1, 'class_name' => $class_name);
  $result = civicrm_api('PaymentProcessor', 'get', $params);
  if (0 == $result['is_error'] && count($result['values']) > 0) {
    foreach($result['values'] as $paymentProcessor) {
      $id = $paymentProcessor['id'];
      if ((is_null($processors)) || !empty($processors[$id])) {
        $list[$id] = $paymentProcessor;
      }
    }
  }
  return $list;
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_validateForm
 * Implementing validate hook for handling Error messages for ACH Payment
 * */

function vanco_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
  if(isset($form->_params['VancoACHProcessors'])) {
    if(in_array($form->_submitValues['payment_processor_id'], $form->_params['VancoACHProcessors'])) {
      unset($form->_errors['credit_card_number']);
      unset($form->_errors['cvv2']);
      unset($form->_errors['credit_card_exp_date']);
    }else{
      unset($form->_errors['routing_number']);
      unset($form->_errors['account_number']);
    }
  }

  //Validating amount should not be less than or equal to 5
	if ( $formName == 'CRM_Contribute_Form_Contribution_Main' && $fields['is_recur'] == 0 ){

		require_once "CRM/Utils/Rule.php";

		if ( !empty($fields['amount_other'])) {
			$amt = CRM_Utils_Rule::cleanMoney( $fields['amount_other'] );
			if ($amt <= 5 ){
				$form->_errors['amount_other'] = "Amount cannot be less than or equal to 5 for one time payment";
			}
		} else if ( isset($fields['amount']) ) {
			$amt = CRM_Utils_Rule::cleanMoney( $fields['amount'] );
			if( $amt <= 5 ) {
				$form->_errors['amount'] = "Amount cannot be less than or equal to 5 for recurring payment";
			}
		}
	}
}