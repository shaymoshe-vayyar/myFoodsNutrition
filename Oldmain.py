import myGui
import databaseHandler
import parsingEngine

# getDailyNutritionGoals
import getDailyNutritionGoals
#getDailyNutritionGoals.Parse()

# Gui to get item to scan, show the optional results, maybe with images, optionaly add "סימון מלא"
# Then allow the user to select the item, change item name and it will be stored in the DB
import HandleNutItemDBUserCreation
HandleNutItemDBUserCreation.GuiFoodData()

if True:
    ###
    ## Read List of items to retrieve and from where
    #   1st step: only from foodsdictionary, only URL option
    #   Later: add name search, add USDA site
    itemsUrlParsingDB = databaseHandler.LoadItemsListToRead()

    ## Go over the list, read each item
    #   Read, and translate the units and names to global
    #   Later: Add the items to existing DB
    DBItemsNut = parsingEngine.readItems(itemsUrlParsingDB)

    ## Save Database
    databaseHandler.StoreItemsNutDatabase(DBItemsNut)

else:
    ## Load Database
    DBItems = databaseHandler.LoadItemsNutDatabase()


### Build GUI to search on DB
#myGui.GuiFoodData(DBItems)

### Build the daily Database
#    1st step: Entering by name, time of meal is noon always
#    Later: add time of meal, search while typing, allow inserting quantities in free text, default unit is gram
#           add additional multiple choice of names to the same item
# Daily DB
#   Daily quantity until now per nutrition type
#
# Daily Recommended
#   Gram/CAL per nutrition type
### Build GUI to manage the daily items
# Date of day in ISR
DBDailyData = databaseHandler.LoadItemsNutDatabase()
myGui.GuiFoodDailyManager(DBItems, DBDailyData)

print('here')



