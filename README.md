bigtwo
======

*CSCI4140 - Group Project - Police Station - Big Two*

* Descripton: https://docs.google.com/document/d/1IMGcqquRaXYdREltNZ28DC4AmG4i03l9QFKFKwS9Yfk/edit?usp=sharing
* Javascript Card Game Library (reference): https://github.com/atomantic/JavaScript-Playing-Cards

Representation of cards
=======================

The cards will be assigned a number from 52 down to 1.<br />
**The filename of the PNGs should be changed with respect to this specification**

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
