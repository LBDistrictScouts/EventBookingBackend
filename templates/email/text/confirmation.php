<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \Cake\View\View $this
 * @var \App\Model\Entity\Entry $entry
 */

?>

<?= $entry->event->event_name ?> - Registration Confirmed
Event Date: <?= $this->Time->i18nFormat($entry->event->start_time, 'dd-MMM-yy') ?>


===========================================================

Walking Group Name: "<?= $entry->entry_name ?>"
Contact Email: "<?= $entry->entry_email ?>"
Contact Mobile: "<?= $entry->entry_mobile ?>"

-------------------------------------------

Booking Reference: <?= $entry->event->booking_code ?>-<?= $entry->reference_number ?>

Security Code: <?= $entry->security_code ?>


-------------------------------------------

You will receive an email confirming the above information.
You will need the booking reference and security code to register on the day of the walk.

The Greenway Team
