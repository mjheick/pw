# pw
A centralized password repository

# Usage
Users must be entered into the tbl_password_users table. id=0 is the "administrator" of all the users (the one with all the keys). Anyone with id>0 is a user. "who" is just a database note of the account owner. "pass" is a md5 representation of the password needed to access the account display.

# Improvements
- Move config out of code
- Centralized administration of password table, instead of needing sql knowledge
