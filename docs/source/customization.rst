Implementing custom filters
===========================

What makes a filter
-------------------

A filter is a pure function [#]_ that takes a ``string`` and returns a ``string``. There are three ways to implement
them.

.. [#] "pure" as in *identical arguments must produce identical return values and the function cannot have side-effects*


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
        public function filter(string $text): string
        {
            return $text . '?';
        }
    }

The preferred version is the callable class but *you do you*.

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

Bring your own API
------------------

Since your custom filter chain should probably work in multiple contexts, wrap it in a custom filter function to
use it consistently across your project.

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