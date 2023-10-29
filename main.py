
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
# DatabaseHandler.__host__ = DatabaseHandler.setHost(DatabaseHandler.__host__,'pc')
HandleItemsAndNutValuesDBs.GuiFoodData()

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
