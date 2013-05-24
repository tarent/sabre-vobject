<?php

namespace Sabre\VObject\Property;

use
    Sabre\VObject\Property,
    Sabre\VObject\Parser\MimeDir;

/**
 * Recur property
 *
 * This object represents RECUR properties.
 * These values are just used for RRULE and the now deprecated EXRULE.
 *
 * The RRULE property may look something like this:
 *
 * RRULE:FREQ=MONTHLY;BYDAY=1,2,3;BYHOUR=5.
 *
 * This property exposes this as a key=>value array that is accessible using
 * getParts, and may be set using setParts.
 *
 * @copyright Copyright (C) 2007-2013 fruux GmbH. All rights reserved.
 * @author Evert Pot (http://evertpot.com/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Recur extends Property {

    /**
     * Updates the current value.
     *
     * This may be either a single, or multiple strings in an array.
     *
     * @param string|array $value
     * @return void
     */
    public function setValue($value) {

        if (is_array($value)) {
            $newVal = array();
            foreach($value as $k=>$v) {
                $newVal[strtoupper($k)] = strtoupper($v);
            }
            $this->value = $newVal;
        } elseif (is_string($value)) {
            $value = strtoupper($value);
            $newValue = array();
            foreach(explode(';', $value) as $part) {

                // Skipping empty parts.
                if (empty($part)) {
                    continue;
                }
                list($partName, $partValue) = explode('=', $part);
                $newValue[$partName] = $partValue;

            }
            $this->value = $newValue;
        } else {
            throw new \InvalidArgumentException('You must either pass a string, or a key=>value array');
        }

    }

    /**
     * Returns the current value.
     *
     * This method will always return a singular value. If this was a
     * multi-value object, some decision will be made first on how to represent
     * it as a string.
     *
     * To get the correct multi-value version, use getParts.
     *
     * @return string
     */
    public function getValue() {

        $out = array();
        foreach($this->value as $key=>$value) {
            $out[] = $key . '=' . $value;
        }
        return strtoupper(implode(';',$out));

    }

    /**
     * Sets a multi-valued property.
     *
     * @param array $parts
     * @return void
     */
    public function setParts(array $parts) {

        $this->setValue($parts);

    }

    /**
     * Returns a multi-valued property.
     *
     * This method always returns an array, if there was only a single value,
     * it will still be wrapped in an array.
     *
     * @return array
     */
    public function getParts() {

        return $this->value;

    }

    /**
     * Sets a raw value coming from a mimedir (iCalendar/vCard) file.
     *
     * This has been 'unfolded', so only 1 line will be passed. Unescaping is
     * not yet done, but parameters are not included.
     *
     * @param string $val
     * @return void
     */
    public function setRawMimeDirValue($val) {

        $this->setValue($val);

    }

    /**
     * Returns a raw mime-dir representation of the value.
     *
     * @return string
     */
    public function getRawMimeDirValue() {

        return $this->getValue();

    }

}
