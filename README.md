=== Vanco Direct Payment Processor ==

- Installation

Go to 'Manage Extensions' page and it will appear as 'Vanco Payments'. Install the extension

Go to Administer -> System Settings -> Payment Processors -> Add Payment Processor

This extension provides 2 payment processors:
1. Vanco Payments Credit Card
2. Vanco Payments ACH

You can add payment processor for either/both options
Add the Vanco credentials in the UserId and password fields
Configure events/contributions pages to use your new processor

- Recurring Contributions Setup

Copy vanco.directpayment.processor/bin/vancoHistory.php to sites/all/modules/civicrm/bin directory
Call Vanco to configure the script path as "<site_url>/sites/all/modules/civicrm/bin/vancoHistory.php"
Vanco will start sending notifications for recurring payments to this script

