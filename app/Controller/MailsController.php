<?php
App::uses('AppController', 'Controller');
/**
 * Mails Controller
 *
 * @property Mail $Mail
 */
class MailsController extends AppController {

/**
 * show method
 *
 * @return void
 */
	public function show() {
		$this->view_show();

		if (!isset($this->params['hash'])) {
			throw new NotFoundException(__('Empty hash'));
		}
		$hash = $this->params['hash'];
		$source = $this->Mail->findByHash($hash);
		if (!$source) {
			throw new NotFoundException(__('Invalid hash'));
		}

		$mail = $this->Mail->parse($source['Mail']['source']);
		$this->set('mail', $mail);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Mail->recursive = 0;
		$this->set('mails', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Mail->id = $id;
		if (!$this->Mail->exists()) {
			throw new NotFoundException(__('Invalid mail'));
		}
		$this->set('mail', $this->Mail->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Mail->create();
			if ($this->Mail->save($this->request->data)) {
				$this->Session->setFlash(__('The mail has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The mail could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Mail->id = $id;
		if (!$this->Mail->exists()) {
			throw new NotFoundException(__('Invalid mail'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Mail->save($this->request->data)) {
				$this->Session->setFlash(__('The mail has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The mail could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Mail->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Mail->id = $id;
		if (!$this->Mail->exists()) {
			throw new NotFoundException(__('Invalid mail'));
		}
		if ($this->Mail->delete()) {
			$this->Session->setFlash(__('Mail deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Mail was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
