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
 * @var \App\View\AppView $this
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="EN-GB" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?= $this->fetch('title') ?></title>
</head>
<body style="margin:0; padding:0; font-family: 'Nunito Sans', Arial, sans-serif; background-color: #f4f4f4;">
    <table
        role="presentation"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        style="background-color: #f4f4f4; padding: 20px;"
    >
        <tr>
            <td align="center">
                <?= $this->fetch('content') ?>
            </td>
        </tr>
    </table>
</body>
</html>
