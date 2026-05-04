<?php

/* File: ./custom/include/jobs/EmailValidation.php */

namespace Sugarcrm\Sugarcrm\custom\inc\jobs;

use SchedulersJob;
use RunnableSchedulerJob;

class EmailValidation implements RunnableSchedulerJob
{
  protected $job;
  protected $limit = 1000;

  public function setJob(SchedulersJob $job)
  {
      $this->job = $job;
  }

  public function run($data)
  {
		//Start
		$GLOBALS['log']->info('Starting Email Address Validation Job');

		$total = $this->processEmailAddresses($data);

	  //END
    $this->job->succeedJob('Email Address Validation Job Succeeded');
    return true;
  }

	//Let's process email addresses
	public function processEmailAddresses($data)
	{
    //Setup queue table
    $query = "INSERT INTO record_queue ";
    $query .= "SELECT id, '0' FROM email_addresses WHERE deleted = 0 ";
    $query .= "AND id NOT IN (SELECT id FROM record_queue); ";

    $GLOBALS['db']->query($query);

    //Retrieve a batch of email addresses
    $this->retrieveEmailAddresses();
	}

  public function retrieveEmailAddresses()
  {
    //Grab a chunk of unprocessed EmailAddress IDs
    $query = "SELECT id FROM record_queue WHERE processed = 0 LIMIT {$this->limit}; ";

    $results = $GLOBALS['db']->query($query);

    //Iterate through each one to validate it
    while($emailID = $GLOBALS['db']->fetchByAssoc($results))
    {
      $emailAddressID = $emailID['id'];
      $validated = $this->validateEmailAddress($emailAddressID);
    }
  }

  public function validateEmailAddress($id)
  {
    //Grab the email address and run it through validator
    $query = "SELECT email_address FROM email_addresses WHERE id = '{$id}' ";

    $emailAddress = $GLOBALS['db']->getOne($query);

    $isValid = \SugarEmailAddress::isValidEmail($emailAddress);

    if($isValid)
    {
        $this->updateRecordQueue($id, 1);
    } else {
        $this->updateRecordQueue($id, 2);
    }

    return true;
  }

  public function updateRecordQueue($id, $status = 0)
  {
    $query = "UPDATE record_queue SET processed = {$status} WHERE id = '{$id}' LIMIT 1; ";

    $GLOBALS['db']->query($query);
  }
}

?>
