<?php
	$jogo_da_velha = new JogoDaVelha();
	$jogo_da_velha->play();

	class JogoDaVelha {
		public $player_str = '';
		public $enemy_str = '';

		public $player_list = array('x', 'o', 'j', 'r');

		public $gameboard = array(
			array(' ', ' ', ' '),
			array(' ', ' ', ' '),
			array(' ', ' ', ' ')
		);

		public $game_state = 0;

		public function play() {
			while(true) {
				
				if($this->game_state == 0) {
					// waiting start
					echo "Jogo da Velha =)" . PHP_EOL . PHP_EOL;
					$player = readline('Selecione um jogador ' . json_encode($this->player_list) . ': ');

					// check if exists on player list
					if(!in_array($player, $this->player_list)) {
						echo "Por favor escolha um jogador válido =)" . PHP_EOL;

						sleep(1);
						continue;
					}

					// select player and remove from the list
					$this->player_str = $player;
					unset($this->player_list[array_search($player, $this->player_list)]);

					// select enemy and remove from the list
					$this->enemy_str = $this->player_list[array_key_first($this->player_list)];
					unset($this->player_list[array_key_first($this->player_list)]);

					echo "Jogador selecionado: ${player}." . PHP_EOL . PHP_EOL;
					echo "Iniciando jogo..." . PHP_EOL;

					// random player
					$this->game_state = random_int(1, 2);

					sleep(1);

					// call pos move
					$this->posMove();
				} else if($this->game_state == 1) {
					// player round

					$x = intval(readline('Posição X: '));

					// check if is a valid position
					if($x <= 0 OR $x > 3) {
						echo "Por favor digite um número de 1 a 3" . PHP_EOL;

						sleep(1);
						continue;
					}

					$y = intval(readline('Posição Y: '));

					// check if is a valid position
					if($y <= 0 OR $y > 3) {
						echo "Por favor digite um número de 1 a 3" . PHP_EOL;

						sleep(1);
						continue;
					}

					// check if has player in selected position
					if($this->gameboard[$y-1][$x-1] !== ' ') {
						echo "Já existe um jogador nessa posição, escolha outra!" . PHP_EOL;

						sleep(1);
						continue;
					}

					// add player to position
					$this->gameboard[$y-1][$x-1] = $this->player_str;

					// change game state
					$this->game_state = 2;

					// call pos move
					$this->posMove();
				} else {
					// enemy round

					echo PHP_EOL . "Inimigo pensando na jogada..." . PHP_EOL;

					sleep(1);

					// get best move
					$pos = $this->getBestMove();

					// add player to position
					$this->gameboard[$pos[0]][$pos[1]] = $this->enemy_str;

					// change game state
					$this->game_state = 1;

					// call pos move
					$this->posMove();
				}

				sleep(1);
			}
		}

		private function posMove() {
			// clear console
			Utils::clearConsole();

			// draw game to console
			$this->drawGame();
			
			// check if game is finished
			if($this->checkIfFinished()) {
				echo "O jogo terminou!" . PHP_EOL;
				die();
			}

			// check if game is tied
			if($this->checkIfTied()) {
				echo "O jogo empatou!" . PHP_EOL;
				die();
			}
		}

		private function getBestMove() {
			$array = array('attack' => $this->enemy_str, 'def' => $this->player_str);

			foreach($array as $c_player_str) {
				/*
					check

					x | x | x
				*/

				for($y = 0; $y < 3; $y++) {
					if($this->gameboard[$y][0] === $c_player_str AND $this->gameboard[$y][1] === $c_player_str AND $this->gameboard[$y][2] === ' ') {
						return array($y, 2);
					}

					if($this->gameboard[$y][0] === $c_player_str AND $this->gameboard[$y][1] === ' ' AND $this->gameboard[$y][2] === $c_player_str) {
						return array($y, 1);
					}

					if($this->gameboard[$y][0] === ' ' AND $this->gameboard[$y][1] === $c_player_str AND $this->gameboard[$y][2] === $c_player_str) {
						return array($y, 0);
					}
				}

				/* 
					check

					| x |
					| x |
					| x |

				*/

				for($x = 0; $x < 3; $x++) {
					if($this->gameboard[0][$x] === $c_player_str AND $this->gameboard[1][$x] === $c_player_str AND $this->gameboard[2][$x] === ' ') {
						return array(2, $x);
					}

					if($this->gameboard[0][$x] === $c_player_str AND $this->gameboard[1][$x] === ' ' AND $this->gameboard[2][$x] === $c_player_str) {
						return array(1, $x);
					}

					if($this->gameboard[0][$x] === ' ' AND $this->gameboard[1][$x] === $c_player_str AND $this->gameboard[2][$x] === $c_player_str) {
						return array(0, $x);
					}
				}

				/* 
					check \
				*/
				
				if($this->gameboard[0][0] === $c_player_str AND $this->gameboard[1][1] === $c_player_str AND $this->gameboard[2][2] === ' ') {
					return array(2, 2);
				}

				if($this->gameboard[0][0] === $c_player_str AND $this->gameboard[1][1] === ' ' AND $this->gameboard[2][2] === $c_player_str) {
					return array(1, 1);
				}

				if($this->gameboard[0][0] === ' ' AND $this->gameboard[1][1] === $c_player_str AND $this->gameboard[2][2] === $c_player_str) {
					return array(0, 0);
				}

				/* 
					check /
				*/
				
				if($this->gameboard[0][2] === $c_player_str AND $this->gameboard[1][1] === $c_player_str AND $this->gameboard[2][0] === ' ') {
					return array(2, 0);
				}

				if($this->gameboard[0][2] === $c_player_str AND $this->gameboard[1][1] === ' ' AND $this->gameboard[2][0] === $c_player_str) {
					return array(1, 1);
				}

				if($this->gameboard[0][2] === ' ' AND $this->gameboard[1][1] === $c_player_str AND $this->gameboard[2][0] === $c_player_str) {
					return array(0, 2);
				}
			}


			/*
				random pos
			*/

			$valid_pos = array();

			$y = 0;
			$x = 0;

			foreach($this->gameboard as $c_y) {
				foreach($c_y as $c_x) {
					if($this->gameboard[$y][$x] === ' ') $valid_pos[] = array($y, $x);
					$x++;
				}
				$x = 0;
				$y++;
			}

			$random_key = array_rand($valid_pos);

			return $valid_pos[$random_key];
		}


		private function checkIfFinished() {
			for($y = 0; $y < 3; $y++) {
				if(Utils::checkEquals($this->gameboard[$y][0], $this->gameboard[$y][1], $this->gameboard[$y][2])){
					return true;
				}
			}

			for($x = 0; $x < 3; $x++) {
				if(Utils::checkEquals($this->gameboard[0][$x], $this->gameboard[1][$x], $this->gameboard[2][$x])){
					return true;
				}
			}

			if(Utils::checkEquals($this->gameboard[0][0], $this->gameboard[1][1], $this->gameboard[2][2])){
				return true;
			}

			if(Utils::checkEquals($this->gameboard[0][2], $this->gameboard[1][1], $this->gameboard[2][0])){
				return true;
			}
		}

		private function checkIfTied() {
			foreach($this->gameboard as $c_y) {
				foreach($c_y as $c_x) {
					if($c_x === ' ') return false;
				}
			}
			return true;
		}
		
		private function drawGame() {
			$result  = PHP_EOL;
			$result .= sprintf("  %s  |  %s  |  %s  " . PHP_EOL, $this->gameboard[0][0], $this->gameboard[0][1], $this->gameboard[0][2]);
			$result .= "-----|-----|-----" . PHP_EOL;
			$result .= sprintf("  %s  |  %s  |  %s  " . PHP_EOL, $this->gameboard[1][0], $this->gameboard[1][1], $this->gameboard[1][2]);
			$result .= "-----|-----|-----" . PHP_EOL;
			$result .= sprintf("  %s  |  %s  |  %s  " . PHP_EOL, $this->gameboard[2][0], $this->gameboard[2][1], $this->gameboard[2][2]);
			$result .= PHP_EOL;

			echo $result;
		}
	}


	class Utils {
		public static function clearConsole() {
			echo chr(27).chr(91).'H'.chr(27).chr(91).'J'; 
		}

		public static function checkEquals(...$elements) {
			$e = $elements[0];
			foreach($elements as $c_e) if($c_e !== $e OR $c_e === ' ') return false;
			return true;
		}
	}
?>