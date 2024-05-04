<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 * @method \App\Model\Entity\Bookmark[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BookmarksController extends AppController
{
    public function isAuthorized($user)
    {
        // Obtenemos la acción actual de la solicitud
        $action = $this->request->getParam('action');
    
        // Las acciones 'index', 'add' y 'tags' siempre están permitidas
        if (in_array($action, ['index', 'add', 'tags'])) {
            return true;
        }
    
        // Todas las demás acciones requieren un id
        if (!$this->request->getParam('pass.0')) {
            return false;
        }
    
        // Verificamos que el marcador (bookmark) pertenezca al usuario actual
        $id = $this->request->getParam('pass.0');
        $bookmark = $this->Bookmarks->get($id);
        if ($bookmark->user_id == $user['id']) {
            return true;
        }
    
        // Si no se cumple ninguna de las condiciones anteriores, se delega la autorización al método isAuthorized de la clase padre
        return parent::isAuthorized($user);
    }
    
    public function tags()
    {
        // Obtenemos los tags de la solicitud actual
        $tags = $this->request->getParam('pass');
        
        // Buscamos los marcadores (bookmarks) que están etiquetados con los tags proporcionados
        $bookmarks = $this->Bookmarks->find('tagged', [
                'tags' => $tags
            ])
            ->all();
        
        // Establecemos los datos de los marcadores y los tags para pasarlos a la vista
        $this->set([
            'bookmarks' => $bookmarks,
            'tags' => $tags
        ]);
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Configura la paginación para mostrar solo los bookmarks del usuario actual
        $this->paginate = [
            'conditions' => [
                'Bookmarks.user_id' => $this->Auth->user('id'),
            ]
        ];
        
        // Obtiene y asigna los bookmarks del usuario actual para mostrar en la vista
        $this->set('bookmarks', $this->paginate($this->Bookmarks));
        
        // Configura la serialización de los bookmarks para enviarlos como datos serializados
        $this->viewBuilder()->setOption('serialize', ['bookmarks']);
    }
    

    /**
     * View method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Users', 'Tags'],
        ]);

        $this->set(compact('bookmark'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Crea una nueva entidad de Bookmark
        $bookmark = $this->Bookmarks->newEntity([]);
        
        // Verifica si la solicitud es de tipo POST
        if ($this->request->is('post')) {
            // Rellena la entidad de Bookmark con los datos de la solicitud
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());
            
            // Asigna el ID del usuario actual al bookmark
            $bookmark->user_id = $this->Auth->user('id');
            
            // Intenta guardar el bookmark en la base de datos
            if ($this->Bookmarks->save($bookmark)) {
                // Si se guarda exitosamente, muestra un mensaje de éxito y redirige al índice de bookmarks
                $this->Flash->success('El favorito se ha guardado.');
                return $this->redirect(['action' => 'index']);
            }
            // Si no se guarda exitosamente, muestra un mensaje de error
            $this->Flash->error('No se pudo guardar el marcador. Inténtalo de nuevo.');
        }
        
        // Obtiene una lista de tags para usar en la vista
        $tags = $this->Bookmarks->Tags->find('list')->all();
        
        // Asigna los datos de bookmark y tags a la vista
        $this->set(compact('bookmark', 'tags'));
        
        // Configura la serialización de la entidad bookmark
        $this->viewBuilder()->setOption('serialize', ['bookmark']);
    }
    

    /**
     * Edit method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // Obtiene el bookmark con el id proporcionado, incluyendo sus tags asociados
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Tags']
        ]);
        
        // Verifica si la solicitud es de tipo PATCH, POST o PUT
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Rellena la entidad de Bookmark con los datos de la solicitud
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());
            
            // Asigna el ID del usuario actual al bookmark
            $bookmark->user_id = $this->Auth->user('id');
            
            // Intenta guardar el bookmark en la base de datos
            if ($this->Bookmarks->save($bookmark)) {
                // Si se guarda exitosamente, muestra un mensaje de éxito y redirige al índice de bookmarks
                $this->Flash->success('The bookmark has been saved.');
                return $this->redirect(['action' => 'index']);
            }
            // Si no se guarda exitosamente, muestra un mensaje de error
            $this->Flash->error('The bookmark could not be saved. Please, try again.');
        }
        
        // Obtiene una lista de tags para usar en la vista
        $tags = $this->Bookmarks->Tags->find('list')->all();
        
        // Asigna los datos de bookmark y tags a la vista
        $this->set(compact('bookmark', 'tags'));
        
        // Configura la serialización de la entidad bookmark
        $this->viewBuilder()->setOption('serialize', ['bookmark']);
    }
    
    /**
     * Delete method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bookmark = $this->Bookmarks->get($id);
        if ($this->Bookmarks->delete($bookmark)) {
            $this->Flash->success(__('The bookmark has been deleted.'));
        } else {
            $this->Flash->error(__('The bookmark could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
