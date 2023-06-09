Implementing custom filters
===========================


What makes a filter
-------------------

A filter is a *pure function* [#]_ that takes a ``string`` and returns a ``string``. There are three ways to implement
them.

.. [#] "pure" as in *identical arguments must produce identical return values* and *the function cannot have
    side-effects*


Classic function
~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    function append_question_mark(string $text): string
    {
        return $text . '?';
    }


Anonymous function
~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    $appendQuestionMark = static fn (string $text): string => $text . '?';


Callable class
~~~~~~~~~~~~~~

.. code-block:: php

    <?php
    use Uffff\Contract\Filter;

    /**
     * @psalm-immutable
     */
    readonly class AppendQuestionMark implements Filter
    {
        public function __invoke(string $text): string
        {
            return $text . '?';
        }
    }

The preferred version is to use a callable class but *you do you*.


Building a custom filter chain
------------------------------

To build filter chains ``Uffff\Builder\FilterBuilder`` exists. The ``build`` method returns a function that chains
all configured filters and adheres to the contract of any filter, meaning it takes a single argument of type ``string``
and returns a ``string``.

.. code-block:: php

    <?php
    use Uffff\Builder\FilterBuilder;

    $filter = (new FilterBuilder())
        ->build();

    $text = $filter('some text');

To add a custom filter, call ``add`` on the builder object.

.. code-block:: php

    <?php
    use Uffff\Builder\FilterBuilder;
    use App\QuestionMarkFilter;

    $filter = (new FilterBuilder())
        ->add(new QuestionMarkFilter())
        ->build();

    $text = $filter('some text');

.. _bring-your-own-api:

Bring your own API
------------------

Since your custom filter chain most likely needs to work in exactly the same way in many places, wrap it in a custom
filter function to provide your own internal API.

.. code-block:: php

    <?php
    namespace App;

    use Uffff\Builder\FilterBuilder;
    use App\QuestionMarkFilter;

    function questionable_unicode(string $text): string
    {
        static $filter = null;

        $filter ??= (new FilterBuilder())
            ->add(new QuestionMarkFilter())
            ->build();

        return $filter($text);
    }

    function questionable_unicode_or_null(?string $text)
    {
        if ($text === null) {
            return null;
        }

        return questionable_unicode($text);
    }