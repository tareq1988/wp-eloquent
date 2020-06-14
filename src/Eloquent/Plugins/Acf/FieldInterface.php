<?php

namespace AmphiBee\Eloquent\Plugins\Acf;

use AmphiBee\Eloquent\Model\Post;

/**
 * Interface FieldInterface.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
interface FieldInterface
{
    /**
     * @param string $fieldName
     */
    public function process($fieldName);

    /**
     * @return mixed
     */
    public function get();
}

/*

Image -> post_id
File -> post_id
Textarea -> text
Text -> text
Repeater -> element's count

1. get postmeta row where post_id = $post->id and meta_key = _field_name OK
2. get the field_key column value and search on posts OK
3. get post where post_name == field_key OK
4. get post_content unserialize to get the field type OK

if repeater:

5. get meta where post_id = $post->id and meta_key like _field_name_%
6. expected result for repeater

7562	2442	_fake_repeater_0_name	field_57f3a84247888
7564	2442	_fake_repeater_0_avatar	field_57f3a84847889
7566	2442	_fake_repeater_1_name	field_57f3a84247888
7568	2442	_fake_repeater_1_avatar	field_57f3a84847889
7596	2442	_fake_repeater_2_name	field_57f3a84247888
7598	2442	_fake_repeater_2_avatar	field_57f3a84847889

in this case we have 2 fields: name and avatar
get the field_key for each one post_name in (field_57f3a84247888, field_57f3a84847889)
get post where post_name  = field_key in()
get the field type on post_content unserialize
repeat the process

*/
