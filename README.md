[![Build Status](https://travis-ci.org/Hansel23/Dictionaries.svg?branch=master)](https://travis-ci.org/Hansel23/Dictionaries)
[![Coverage Status](https://coveralls.io/repos/github/Hansel23/Dictionaries/badge.svg?branch=master)](https://coveralls.io/github/Hansel23/Dictionaries?branch=master)
[![Latest Stable Version](https://poser.pugx.org/hansel23/dictionaries/v/stable)](https://packagist.org/packages/hansel23/dictionaries)
[![Latest Unstable Version](https://poser.pugx.org/hansel23/dictionaries/v/unstable)](https://packagist.org/packages/hansel23/dictionaries)
[![Total Downloads](https://poser.pugx.org/hansel23/dictionaries/downloads)](https://packagist.org/packages/hansel23/dictionaries)
[![License](https://poser.pugx.org/hansel23/dictionaries/license)](https://packagist.org/packages/hansel23/dictionaries)

# Dictionary

Dictionary Type. Keys and values are strongly typed, so you will know what you get.

## Usage

Immagine you have a class with member data called Member.
A member has a firstnamer, lastname and an age. To identify a member you have the type MemberId.

Now you want to have an array where you can put the members into. 
Now it's a nice idea to get the member information of a specific member by accessing the array with the MemberId.
But you can't set the MemberId as the key. 

With the Dictionary type this problem is history!

You simply create a Dictionary with the MemberId as the key and the Member as the value like this:

	<?php
	$members = new Dictionary( MemberId::class, Member::class );
	?>
	
Now you can add your members to the dictionary like this:

	<?php	
	$members->add($memberId, $member);
	?>
    
Where $memberId is of the type MemberId and $member of the type Member.

But you can also add a member with the identifier like you can add a normal key-value-pair (string/int) in a normal array.

	<?php	
	$members[$memberId] = $member;
	?>

**Notice: Your IDE may mark it as an error, but the code will run properly!**

Now if you filled your dictionary with members you can do following:

    <?php
    foreach( $members as $memberId => $member )
    {
        printf( "Id: %s, Name: %s %s, $memberId->toString(), $member->getFirstName(), $member->getLastName() );
    }
    ?>

Or you want to get a specific member of the dictionary by member id:

    <?php
    print_r( $members[$memberId] );
    ?>

Another nice thing is, that you can now better type hint your arrays.
Imagine you create an interface with a public method that needs an array as argument.
But you are expecting a specific type of objects in the array. Sure you can type hint arrays, but do you really want to hope, that everyone is giving you the right array?
Or do you want to validate the right type of objects in the array in your implementations?

Better this way: 

1. Create a new Dictionary class, e.g. MemberDictionary
2. Extend it from the Dictionary.
3. Overwrite the constructor and set the needed type of the key and value.
4. Use your own dictionary as the typehint!

Like this: 
   
    <?php
    use Hansel23/Dictionaries/Dictionary;
    final class MemberDictionary extends Dictionary
    {
        public function __construct( )
        { 
            parent::__construct( MemberId::class, Member::class );
        }
    }
    ?>

**Hint: You can also use scalar types in your dictionary! Look at the following example:**
    
    <?php
    final class StringIntegerDictionary extends Dictionary
    {
        public function __construct()
        {
            parent::__construct( gettype( '' ), gettype( 1 ) );
        }
    }