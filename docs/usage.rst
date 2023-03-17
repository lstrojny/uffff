Basic usage
===========

.. _installation:

Installation
------------

To install **Uffff**, use composer and require ``lstrojny/uffff``.

.. code-block:: console

    $ composer require lstrojny/uffff


Pre-defined filters
-------------------

**Uffff** filters are meant to run on user input before further processing. This is often done centrally in the model
layer of an application.

.. note::
    **Uffff** assumes a string it processes to be UTF-8. It will correct minor mistakes to make it full valid UTF-8 but
    it does not convert the encoding. Should you need to support a different encoding, :ref:`bring-your-own-api` and
    implement encoding conversion before invoking the filter.

.. code-block:: php

    <?php
    namespace App;

    use Uffff\unicode;

    class Entity
    {
        private string $text;

        public function __construct(string $text)
        {
            $this->text = unicode($text);
        }
    }

The default ``unicode`` takes a ``string`` and returns a ``string`` and will apply all available filters in the following
order:

 #. ``Uffff\Filter\StripNullByte``: strips all null-bytes from the input
 #. ``Uffff\Filter\CheckIfUnicode``: throws an exception if the input is not unicode
 #. ``Uffff\Filter\NormalizeForm``: normalized input to *Normalization Form Canonical Composition*
 #. ``Uffff\Filter\HarmonizeNewlines``: harmonizes all newlines to UNIX newlines (``\n``)
 #. ``Uffff\Filter\BalanceBidirectionalMarker``: balances bidirectional marking characters
 #. ``Uffff\Filter\TrimWhitespace``: removes whitespace and control characters from the start and beginning of the input

To support nullable input a null-accepting alternative exists, ``unicode_or_null``. The only difference to `unicode` is
that it additionally takes and returns ``null``. This allows for optional fields as outlined in this example.

.. code-block:: php

    <?php
    namespace App;

    use Uffff\unicode_or_null;

    class Entity
    {
        private string $text;

        public function __construct(?string $text = null)
        {
            $this->text = unicode_or_null($text);
        }
    }

Next to the stock ``unicode`` and ``unicode_or_null`` filters two more functions exists, ``unicode_untrimmed`` and
``unicode_untrimmed_or_null``. The different to ``unicode`` is that the ``TrimWhitespace`` filter is not applied. This
should be used for passwords and password-alike values that could legitimately contain leading or trailing spaces.