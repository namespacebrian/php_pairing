<?php

namespace Drupal\PhpPairing\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\PhpPairing\Game;


/**
 * Returns responses for php_pairing routes.
 */
class PhpPairingController extends ControllerBase {

  public $game;

  /**
   * Builds the response.
   */
  public function build() {

    $this->game = new Game();

    $markup = $this->select_door();

    while ($this->game->getNumberOfClosedDoors() > 2) {
      $opened_door = $this->game->openRandomLosingDoor();
      $markup .= 'Door # ' . $opened_door->getDoorNumber()
        . ' has been opened and behind it is a goat.' . "<br>";
    
      $remaining_closed_doors = $this->game->getClosedDoorNumbers();
    
      $markup .= '<br>One of these remaining doors is a winner: '
        . implode(', ', $remaining_closed_doors) . "<br><br>";
    
      $question =  'Will you change your selection? (yes/no) <br>';
    
      $markup .= $question;

      $options = ['yes', 'no'];
      $will_reselect = array_rand($options);

      $markup .= $options[$will_reselect] . "<br>";
      
      if ($options[$will_reselect] == "yes") {
        $markup .= 'You will reselect a door.' . "<br>";
        $markup .= $this->select_door();
      }
    }
    
    $opened_door = $this->game->openSelectedDoor()->getDoorNumber();
    $markup .=  "You open door # {$opened_door}...<br>";
    if ($this->game->isWon()) {
      $markup .=  "You won the car!<br><br>";
    } else {
      $markup .=  "You got the goat :sad-trombone:<br><br>";
    }

    
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $markup,
    ];

    return $build;
  }

  public function select_door() {
    $output = "";
    $door_selection_options = $this->game->getDoorSelectionOptions();

    if (count($door_selection_options) > 1) {
      $output = 'Select a door from these options [' . implode(', ', $this->game->getDoorSelectionOptions()) . "]<br><br> ";
  
      // $door_selection = readline($question);
      $door_selection = rand(1, 3);
    } else {
      $door_selection  = array_shift($door_selection_options);
    }
    $output .= "Your selected door is # {$door_selection}<br><br>";
    $this->game->selectDoor((int) $door_selection);

    return $output;
  }

}
