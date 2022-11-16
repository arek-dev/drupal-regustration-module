<?php

/**
 * @file
 * Contains \Drupal\singleregistration\Form\SingleRegistrationForm.
 */
namespace Drupal\singleregistration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SingleRegistrationForm extends FormBase {

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Node storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeManager;

  public function __construct(
    EntityTypeManager $entity_type_manager,
    AccountProxyInterface $current_user
  ) {
    $this->currentUser = $current_user;
    $this->nodeManager = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'singleregistrationform';
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['employee_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter Name:'),      
      '#required' => TRUE,
    );
    $form['one_plus'] = array (
        '#type' => 'checkbox',
        '#title' => t('One Plus?'),        
    );
    $form['amount_of_kids'] = array(
      '#type' => 'number',
      '#min' => 0,
      '#title' => t('Amount of kids:'),
      '#required' => TRUE,
    );
    $form['amount_of_vegetarians'] = array(
        '#type' => 'number',
        '#min' => 0,
        '#title' => t('Amount of vegetarians:'),
        '#required' => TRUE,
      );
    $form['email_address'] = array(
      '#type' => 'email',
      '#title' => t('Enter Email:'),
      '#required' => TRUE,
    );    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Register'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'registration')
      ->condition('field_email_address', $form_state->getValue('email_address'))
      ->range(0,4)
      ->execute();

      if(!empty($ids)){
        $form_state->setErrorByName('email_address', $this->t('This email already exists'));
      }


    if(
      ($form_state->getValue('one_plus') == 1) && ($form_state->getValue('amount_of_vegetarians')
      > ($form_state->getValue('amount_of_kids') + 2))
      ||
      ($form_state->getValue('one_plus') != 1) && ($form_state->getValue('amount_of_vegetarians')
      > ($form_state->getValue('amount_of_kids') + 1))
      )
      {
      $form_state->setErrorByName('amount_of_vegetarians', $this->t('Please enter a valid number of vegetarians'));
    }    
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node = $this->nodeManager->create([
      'type' => 'registration',
      'title' => $form_state->getValue('employee_name'),
      'uid' => $this->currentUser->id(),
      'status' => 1,
    ]);
    
    $node->field_employee_name->value = $form_state->getValue('employee_name');
    $node->field_one_plus->value = $form_state->getValue('one_plus');
    $node->field_amount_of_kids->value = $form_state->getValue('amount_of_kids');
    $node->field_amount_of_vegetarians->value = $form_state->getValue('amount_of_vegetarians');
    $node->field_email_address->value = $form_state->getValue('email_address');
    print dpm($form_state->getValue('one_plus'));


    $node->save();
  }


  // public function submitForm(array &$form, FormStateInterface $form_state) {
  //   \Drupal::messenger()->addMessage(t("Your Registration Done with below data:"));
	// foreach ($form_state->getValues() as $key => $value) {
	//   \Drupal::messenger()->addMessage($key . ': ' . $value);
  //   }
  // }
}