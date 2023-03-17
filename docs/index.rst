Uffff – *Unicode input processing made trivial!*
================================================

**Uffff**, pronounced as *oof* (/uːf/), the sound one makes when realizing the amount of work necessary to properly
process unicode. It’s also a unicode-speak play on the unicode code point ``U+ffff``. If you prefer, it could also stand
for *unicode’s fabulously functional figure filter*.

Processing user-supplied unicode text correctly is surprisingly difficult. Normalization to a well-known form is
required, balancing opening directional markers, stripping null bytes, potentially trimming esoteric whitespaces,
harmonizing newlines and more.

This is what **Uffff** specializes in. It offers both a high-level API that should solve the problem for 90% of the
use cases as well as an extensible architecture to implement custom filters.

Learn about high-level :doc:`usage` including :ref:`installation` of the library and dive deeper into
:doc:`customization` on how to implement your own filters.


Contents
--------

.. toctree::
   :maxdepth: 2

   usage
   customization
