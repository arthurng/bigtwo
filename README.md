bigtwo
======

*CSCI4140 - Group Project - Police Station - Big Two*

* Descripton: https://docs.google.com/document/d/1IMGcqquRaXYdREltNZ28DC4AmG4i03l9QFKFKwS9Yfk/edit?usp=sharing
* Javascript Card Game Library (reference): https://github.com/atomantic/JavaScript-Playing-Cards

Representation of cards
=======================

The cards will be assigned a number from 52 down to 1.<br />
*The PNGs should be named following this convention*

The largest, *Two of Spades* would have a value of 52.<br />
The second, *Two of Hearts* would have a value of 51.<br />
.<br />
.<br />
The smallest, *Three of Diamonds* would have a value of 1.<br />


Rank of Hands
=============

The rank of a hand will be represented as follow<br />
The rank value should be stored in a string as shown:

`The rank of the hand itself`-`The largest card in the hand`

For example: <br />

6-49 = A Full House with **Ace of Diamonds** being the largest card.<br />
2-3 = A Pair of cards with **Three of Hearts** being the largest card.<br />

**The mapping of the hand to the numeric representation**

|   Name of hand  | Value   |
|:----------------|:-------:|
| Straight Flush  | 8       |
| Four of a Kind  | 7       |
| Full House      | 6       |
| Flush           | 5       |
| Straight        | 4       |
| Three of a Kind | 3       |
| Pair            | 2       |
| Single          | 1       |

Hence, a 5-32 hand would beat a 4-52 hand.

Storage of hand history
=======================

At least **four** previous hands should be stored in the database.<br />
History of hands would allow the system to determine if the user can dealt a card freely.<br />
note: `PASS` should be stored in case of a player forfeiting his/her change to deal a hand.

database entry: `firstLast`, `secondLast`, `thirdLast`, `forthLast`

The program should include codes to push the value back a cell when a new value is written to `last`.<br />
i.e. `firstLast` => `secondLast`, `secondLast` => `thirdLast` and etc.
