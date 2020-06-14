<?php

namespace AmphiBee\Eloquent\Plugins\Acf\Field;

use Carbon\Carbon;
use AmphiBee\Eloquent\Plugins\Acf\FieldInterface;
use AmphiBee\Eloquent\Model\Post;

/**
 * Class DateTime.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class DateTime extends BasicField implements FieldInterface
{
    /**
     * @var Carbon
     */
    protected $date;

    /**
     * @param string $fieldName
     */
    public function process($fieldName)
    {
        $dateString = $this->fetchValue($fieldName);
        $format = $this->getDateFormatFromString($dateString);
        $this->date = Carbon::createFromFormat($format, $dateString);
    }

    /**
     * @return Carbon
     */
    public function get()
    {
        return $this->date;
    }

    /**
     * @param string $dateString
     *
     * @return string
     */
    protected function getDateFormatFromString($dateString)
    {
        if (preg_match('/^\d{8}$/', $dateString)) { // 20161013 => date only
            return 'Ymd';
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateString)) { // 2016-10-19 08:06:05
            return 'Y-m-d H:i:s';
        } elseif (preg_match('/^\d{2}:\d{2}:\d{2}$/', $dateString)) { // 17:30:00
            return 'H:i:s';
        }
    }
}
