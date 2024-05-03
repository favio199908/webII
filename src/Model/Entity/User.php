<?php
declare(strict_types=1);

namespace App\Model\Entity;
use Cake\Auth\DefaultPasswordHasher; // Incluimos la clase DefaultPasswordHasher para el hashing de contraseñas
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Bookmark[] $bookmarks
 */
class User extends Entity
{

        // Este método es llamado automáticamente cuando se establece el valor de la propiedad 'password'
    protected function _setPassword($value)
    {
        // Creamos una instancia de DefaultPasswordHasher para encriptar la contraseña
        $hasher = new DefaultPasswordHasher();
        
        // Devolvemos la contraseña encriptada utilizando el método hash() del objeto DefaultPasswordHasher
        return $hasher->hash($value);
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
        'email' => true,
        'password' => true,
        'created' => true,
        'modified' => true,
        'bookmarks' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected $_hidden = [
        'password',
    ];
}
