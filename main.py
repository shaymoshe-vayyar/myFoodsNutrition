#
# קישוא מבושל 25
# חציל מבושל 25
# פטריות מבושלות 25
# גבינה 10
# קנולי 15
# לחם שום 10
# זית 5

# # Tmp
# import USDAParsing
# USDAParsing.get_nutrition_values('')

# # App1
# # Gui to get item to scan, show the optional results, maybe with images, optionaly add "סימון מלא"
# # Then allow the user to select the item, change item name and it will be stored in the DB
import HandleItemsAndNutValuesDBs

import database_handler
dbh = database_handler.DatabaseHandler(['pc','web'])
HandleItemsAndNutValuesDBs.GuiFoodData(dbh)
# HandleItemsAndNutValuesDBs.GuiFoodDataEdit(dbh)

# App2

# TODO:
#
#
# Allow multiple names for searching
#
# Fix מכניס פעמיים - מחק שורה, הכנס
# Add option to select from multiple matches
# Find similar match (2 words inserted with one word can be inbetween, different order)
# Website display - Add different views
# Add last inserted items for last hour
#
# Add DB From USDA - think about the design.
# nut values  UL,

# Create all tables from Python
#
# Improvements:
# * Allow search for synomynon, better searching (different words order, give more weight to first words, search basic items before extended, ignore words in parenthsis etc...)
# * Add data from USDA DB, with the option of combining from several resources
# * Add recipe analyzer
# * Add option to add data from online website
# * Add data regarding the items category that are missing. e.g. green, orange, yellow etc. vegt. Grain quantity, nuts etc.
# * Remove sources table and include it in the items nut db file
# * Combine the goals, upper limits, display units per nut. tables

# Tables:
#   Nutrition attributes with nut. unique ID, nut. name, nut. daily Goal, nut. daily UL, nut display per unit, additional names (english/hebrew)?
#   Items list with item's unique ID (auto index), user unique ID?, item name - index (+additional names?), Nutrition Values, type/category (e.g. green veg., nuts, etc),
#                           source(s), isExtended, isCombined, item description (including recipe?), item photo link?
#   Daily items list - unique ID (auto Index), user unique ID?, list of date, time, item, quantity
#   Translation table - unique id (Auto Index), heb<->eng. for units, [nut. names?, item names? - better maintanance in their tables, to minimize pointers/duplicated lists from tables]

# New App 0 - build DBs

# TODO:
# * Update UL Goals, daily goals cross check from US health doc
# * Fix Safari support
# * Fix פיצה אישית
