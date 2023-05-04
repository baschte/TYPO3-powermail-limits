<?php

namespace Jpmschuler\PowermailLimits\DataProcessor;

use In2code\Powermail\DataProcessor\AbstractDataProcessor;
use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use Jpmschuler\PowermailLimits\Domain\Model\FormWithSubmissionLimit;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class DoSomethingDataProcessor
 */
class SubmissionLimitDataProcessor extends AbstractDataProcessor
{
    protected function addNewValuesToMail(array $newData): void
    {
        foreach ($newData as $label => $data) {
            $answer = new Answer();
            $answer->_setProperty('translateFormat', 'Y-m-d');
            $answer->_setProperty('valueType', 0);
            $field = new Field();
            $field->setType('input');
            $field->setTitle($label);
            $answer->_setProperty('name', $label);
            $answer->_setProperty('value', print_r($data, true));
            $answer->setValueType(0);
            $answer->setField($field);
            $this->getMail()->addAnswer($answer);
        }
    }

    public function addFieldsDataProcessor(): void
    {
        $mail = $this->getMail();
        /* @var $form FormWithSubmissionLimit */
        $form = $mail->getForm();

        if ($form->submissionlimit) {
            $addToOutput = [];

            $labelSubmissionLimit = LocalizationUtility::translate(
                'form.submissionstatus',
                'powermail_limits'
            );

            if ($form->isNewSubmissionForWaitlist()) {
                $addToOutput[$labelSubmissionLimit] = LocalizationUtility::translate(
                    'form.submissionstatus.waitinglist',
                    'powermail_limits'
                );
            } elseif ($form->isNewSubmissionValid()) {
                $addToOutput[$labelSubmissionLimit] = LocalizationUtility::translate(
                    'form.submissionstatus.valid',
                    'powermail_limits'
                );
            } else {
                $addToOutput[$labelSubmissionLimit] = LocalizationUtility::translate(
                    'form.submissionstatus.invalid',
                    'powermail_limits'
                );
            }
            $this->addNewValuesToMail($addToOutput);
        }
    }
}
