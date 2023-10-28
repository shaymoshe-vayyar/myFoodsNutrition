from database_handler import DatabaseHandler

# App 0
import createBasicTablesDB
# createBasicTablesDB.createEngHebTermsTableDB()
# createBasicTablesDB.createNutValuesTableDB()
# createBasicTablesDB.createNutUnitsToDisplayTableDB()
# createBasicTablesDB.createDailyNutGoalsTableDB()


# App1
# Gui to get item to scan, show the optional results, maybe with images, optionaly add "סימון מלא"
# Then allow the user to select the item, change item name and it will be stored in the DB
import HandleItemsAndNutValuesDBs
# DatabaseHandler.__host__ = DatabaseHandler.setHost(DatabaseHandler.__host__,'pc')
HandleItemsAndNutValuesDBs.GuiFoodData()

# App2
# Recreate DBs: ItemNutValues, Empty Daily items, conversion tables (eng-heb names, units), display_units_per_nut_type, nut values goals nad UL,
# Host can be 'pc' or 'web'

# TODO:
#
#
# Create all tables from Python
#
# Nutrition table
#
#
# mySql - close and reopen per access from python, think maybe to access in parallel for both, maybe to build a class to handle it...
#
# Allow multiple names for searching
#
# Fix מכניס פעמיים - מחק שורה, הכנס
# Add option to select from multiple matches
# Find similar match (2 words inserted with one word can be inbetween, different order)
# Add different views
# Add last inserted items for last hour
#
# Add DB From USDA - think about the design.
