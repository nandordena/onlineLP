# How to colab
The project is just getting started. We just have to wait for the foundations to be laid.

## How to start repo
You have instrucions to star in [START.md](/DOCU/START.md)

## Methodology and Philosophy
It is developed based on a `hexagonal infrastructure` where each component functions **independently** and is separate from the rest.  
Each component, classified as `ADAPTER`, has its own server and environment in which to operate, although they can inherit generic material from `CORE`.

## Tecnology
As each **ADAPTER is independent**, you can decide at any time to change the environment for each one, or perhaps create **alternative and parallel ADAPTERs**, but as a rule, the classic vanilla web stack is used: ` PHP-SQL-HTML-CSS `. 