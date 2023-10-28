import csv
from database_handler import DatabaseHandler
import globalContants as gc

## table - eng_heb_terms - to read from csv
def createEngHebTermsTableDB():
    # with open('eng_heb_terms.csv', newline='') as csvfile:
    #     reader = csv.reader(csvfile, delimiter=',', quotechar='"')
    #     for row in reader:
    #         print(', '.join(row))
    tableName = 'eng_heb_terms'
    with open('eng_heb_terms.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        ColNamesNTypes = {colName : 'str' for colName in reader.fieldnames}
        DatabaseHandler().CreateTable(tableName,
                                    ColNamesNTypes, ifExists='replace')

        for row in reader:
            # print(row)
            DatabaseHandler().addItem(tableName,
                                    list(row.keys()),
                                    list(row.values()))

##
def createNutValuesTableDB():
    DatabaseHandler().deleteTable(gc.__tableItemsNutValuesTableName__)
    nutNames = DatabaseHandler().getItem('eng_heb_terms','category','nutrition')
    dictColsNameNType = {nutNames[ii][0]: 'float' for ii in range(len(nutNames))}
    defaultValues = {colName: -1000 for colName in dictColsNameNType.keys()}
    dictColsNameNType['itemName'] = 'str'
    DatabaseHandler().CreateTable(gc.__tableItemsNutValuesTableName__,
                                dictColsNameNType,
                                PrimaryKeyName='itemName',
                                colDefValues=defaultValues,
                                ifExists='replace')


## create 'conversion_units_to_standard' table

def createNutUnitsToDisplayTableDB():
    import HandleConversion
    import foodsdicParsing
    foodsdicParsing.ParseUrl('https://www.foodsdictionary.co.il/Products/1/%D7%97%D7%A1%D7%94%20%D7%A2%D7%A8%D7%91%D7%99%D7%AA')
    foodsdicParsing.ParseUrl('https://www.foodsdictionary.co.il/Products/1/%D7%97%D7%96%D7%94%20%D7%A2%D7%95%D7%A3%20%D7%9E%D7%91%D7%95%D7%A9%D7%9C-%D7%A6%D7%9C%D7%95%D7%99')
    dict_nuctd = foodsdicParsing.__nutDisplayUnitsEng__
    ColNamesNTypes = {'keyCol' : 'str','valueCol' : 'str'}
    DatabaseHandler().deleteTable(gc.__tableConversionNutUnitsToDisplayName__)
    DatabaseHandler().CreateTable(gc.__tableConversionNutUnitsToDisplayName__,
                                ColNamesNTypes, ifExists='replace')

    for k,v in dict_nuctd.items():
        DatabaseHandler().addItem(gc.__tableConversionNutUnitsToDisplayName__,
                                ['keyCol','valueCol'],
                                [k,v])

    # Manual insertions
    DatabaseHandler().addItem(gc.__tableConversionNutUnitsToDisplayName__,
                                    ['keyCol','valueCol'],
                                    ['vitamin_b4','miliGram'])
    DatabaseHandler().addItem(gc.__tableConversionNutUnitsToDisplayName__,
                                    ['keyCol','valueCol'],
                                    ['vitamin_b7','microGram'])
    DatabaseHandler().addItem(gc.__tableConversionNutUnitsToDisplayName__,
                                    ['keyCol','valueCol'],
                                    ['lycopene','miliGram'])



def createDailyNutGoalsTableDB():
    tableName = 'daily_nutrition_goals'
    with open('daily_nutrition_goals.csv', newline='') as csvfile:
        reader = csv.DictReader(csvfile)
        ColNamesNTypes = {colName : 'str' for colName in reader.fieldnames}
        DatabaseHandler().CreateTable(tableName,
                                    ColNamesNTypes, ifExists='replace')

        for row in reader:
            # print(row)
            DatabaseHandler().addItem(tableName,
                                    list(row.keys()),
                                    list(row.values()))

