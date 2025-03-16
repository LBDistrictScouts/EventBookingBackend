<?php

/** Email Template
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Entry $entry
 */

?>

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 5px; padding: 20px;">
    <tr>
        <td align="center" style="padding-bottom: 20px;">
            <h1 style="margin: 0; color: #333;"><?= h($entry->event->event_name) ?> Booking</h1>
            <h2 style="padding-top: 0px; color: #6c757d;"><?=
                $this->Time->i18nFormat($entry->event->start_time, 'dd-MMM-yy')
            ?></h2>
        </td>
    </tr>
</table>

<br />

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background: #f4f4f4; padding: 20px;">
    <tr>
        <td align="center">
            <table
                role="presentation"
                width="600"
                cellpadding="0"
                cellspacing="0"
                style="background: #d4edda; border-radius: 6px; padding: 20px; border: 1px solid #c3e6cb;"
            >
                <tr>
                    <td style="font-family: 'Nunito Sans', Arial, sans-serif;">
                        <h2 style="margin: 0 0 10px 0; color: #155724;">Registration Confirmed</h2>
                        <p style="margin: 10px 0;">Walking Group Name: <strong>"<?=
                                $entry->entry_name
                        ?>"</strong></p>
                        <p style="margin: 10px 0;">Contact Email: <strong>"<?=
                                $entry->entry_email
                        ?>"</strong></p>
                        <p style="margin: 10px 0 20px 0;">Contact Mobile: <strong>"<?=
                                $entry->entry_mobile
                        ?>"</strong></p>

                        <table
                            role="presentation"
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            style="background: #ffffff; border-radius: 6px; padding: 20px;
                                border: 1px solid #e0e0e0; margin-bottom: 20px;"
                        >
                            <tr>
                                <td style="width: 50%; text-align: center; padding: 10px;">
                                    <p style="font-size: 24px; margin: 0; font-weight: bold;">
                                        <?= $entry->event->booking_code ?>-<?= $entry->reference_number ?></p>
                                    <p style="margin: 5px 0 0 0; color: #6c757d;">Booking Reference</p>
                                </td>
                                <td style="width: 50%; text-align: center; padding: 10px;">
                                    <p style="font-size: 24px; margin: 0; font-weight: bold;">
                                        <?= $entry->security_code ?>
                                    </p>
                                    <p style="margin: 5px 0 0 0; color: #6c757d;">Security Code</p>
                                </td>
                            </tr>
                        </table>

                        <p style="color: #333; font-size: 14px;">You will need the booking reference and security code to register on the day of the walk.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br />

<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 5px; padding: 20px;">
    <tr>
        <td style="color: #555;">
            <p>You are receiving this email because a reservation was added in your name.</p>
            <p>Your booking was created at <?= $this->Time->i18nFormat($entry->created, 'HH:mm', 'Europe/London') ?> on <?= $this->Time->i18nFormat($entry->created, 'dd-MMM-yy', 'Europe/London') ?>.</p>
            <p>If this wasn't you, please email <a href="mailto:greenway@lbdscouts.org.uk" style="color: #28a745;">greenway@lbdscouts.org.uk</a>.</p>
        </td>
    </tr>
</table>
