import DatabaseHandler

# App 0
# import createBasicTablesDB
# createBasicTablesDB.createEngHebTermsTableDB()
# createBasicTablesDB.createNutValuesTableDB()
# createBasicTablesDB.createNutUnitsToDisplayTableDB()
# createBasicTablesDB.createDailyNutGoalsTableDB()

# App1
# Gui to get item to scan, show the optional results, maybe with images, optionaly add "סימון מלא"
# Then allow the user to select the item, change item name and it will be stored in the DB
import HandleItemsAndNutValuesDBs
DatabaseHandler.__host__ = DatabaseHandler.setHost(DatabaseHandler.__host__,'pc')
HandleItemsAndNutValuesDBs.GuiFoodData()

# App2
# Recreate DBs: ItemNutValues, Empty Daily items, conversion tables (eng-heb names, units), display_units_per_nut_type, nut values goals nad UL,
# Host can be 'pc' or 'web'

# TODO:
# Add tables of eng translation from CSV
#
# ***Done*** Add url link per name and store in a table
#
# Fix פחמימות
# לתקן נתרן
# להוסיף תרגום מדוייק מעברית, לתקן שפחמימות ומתוכן סוכרים מתרגם לאותה מילה באנגלית
# האם לעשות את הדיפולט NULLאו אפס
#
# Create all tables from Python
#
# Nutrition table
# Add defaults to all Nut cols.
#
#
# Fix webpage - add change and remove for items, add תפריט view, add iphone audio commands - מחק שורה, הכנס
#
#
# Add DB From USDA