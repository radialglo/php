PHP playground
===

###### 1. Stack
- Object Oriented Stack with push and pop functionality
- retreives smallest element in O(1)

###### 2. Calculator
- Form takes string as input and sends input to server as POST parameter
- eval to evaluate arithmetic expression (tested locally to avoid malicious exploitation)
- Allowed Characters:
 - +
 - -
 - *
 - /
 - 0-9
- Implementation
 - White space is considered invalid 
 - Check for any invalid characters
 - Extract operands as an array
  - check for repeated decimals and extra whitespace
 - Check Operators
  - Operators at beginning/end of string
  - two operators (not including [+*/]- ) or - [+-*/] 
  - division by 0 and any other forms such as +0,.0, 0.
- Exceptions
 - Parantheses are not considered valid in this implementation 
  - -- is considered invalid, cannot distinguish between subtraction and negation
  - In the case where we have  [+*/]- , - is interpreted as negation