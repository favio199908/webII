<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Collection\Collection;

/**
 * Bookmark Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $url
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Tag[] $tags
 */
    
class Bookmark extends Entity
{
    protected function _getTagString()
    {
        // Verifica si ya se ha obtenido el tag_string anteriormente y lo devuelve si es así
        if (isset($this->_fields['tag_string'])) {
            return $this->_fields['tag_string'];
        }
        
        // Si no se ha obtenido el tag_string y no hay tags asociados, devuelve una cadena vacía
        if (empty($this->tags)) {
            return '';
        }
        
        // Crea una colección de tags
        $tags = new Collection($this->tags);
        
        // Reduce la colección de tags a una cadena de tags separados por coma y espacio
        $str = $tags->reduce(function ($string, $tag) {
            return $string . $tag->title . ', ';
        }, '');
        
        // Elimina la coma y el espacio final y devuelve la cadena resultante
        return trim($str, ', ');
    }
    
    
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'user_id' => true,
        'title' => true,
        'description' => true,
        'url' => true,
        'user' => true,
        'tags' => true,
        'tag_string' => true,
    ];
}
