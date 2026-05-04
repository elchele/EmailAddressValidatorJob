# EmailAddressValidatorJob
Sugar Scheduler Job for validating Email Addresses

This Scheduler Job can be used to iterate through all email addresses contained within a given instance to determine if they are valid.

While the Sugar UI will validate email addresses upon data entry, it is possible that some Use Cases implement procedures that result in the creation of invalid or improperly formatted email addresses. For example, using SQL queries to execute INSERT statements that create email address records.

Invalid email address example: john@doe@example.com 

The job uses a table named *record_queue* to track the processing of the email addresses. If an email address is found to be invalid, the job will mark the corresponding ID of that entry with a status of '2' within the record_queue table.
