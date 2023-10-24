import DatabaseHandler

## Constants
__gram__ = 'gram'
__microGram__ = 'microGram'
__miliGram__ = 'miliGram'
__microGram__ = 'microGram'
__kGram__ = 'kGram'
__Cal__ = 'Cal'
__tspn__ = 'tspn'

##


nutNameList = ['חומצת שומן אולאית-אומגה 9',
               'חומצות שומן חד בלתי רוויות',
       'חומצה לינולאית-אומגה 6',
               'חומצה אלפא לינולנית-אומגה 3',
       'חומצות שומן רב בלתי רוויות', 'אשלגן', 'אבץ', 'זרחן', 'מגנזיום', 'ברזל',
       'סידן', 'ויטמין K', 'ויטמין E', 'ויטמין C', 'חומצה פולית - ויטמין B9',
       'ויטמין B6', 'ויטמין B5', 'ויטמין B3 - ניאצין', 'ויטמין B2',
       'ויטמין B1', 'סה"כ ויטמין B', 'ויטמין A', 'ליקופן', 'מים',
       'סיבים תזונתיים', 'נתרן', 'כולסטרול', 'מתוכם שומן רווי', 'שומנים',
       'כפיות סוככפיות סוכר', 'מתוכן סוכרים', 'פחמימות', 'חלבונים',
       'קלוריות']


def LoadTermTranslationTables():
    table_name = 'Eng_heb_terms'
    keys_values = DatabaseHandler.loadAllRows(table_name, ['eng_name', 'heb_name'])
    engHebDict = {keys_values[i][0]: keys_values[i][1] for i in range(len(keys_values))}
    HebEngDict = {keys_values[i][1]: keys_values[i][0] for i in range(len(keys_values))}
    return engHebDict, HebEngDict

__dictEngNameToHebName__,__dictHebNameToEngName__ = LoadTermTranslationTables()

__dictNutNameToUnitsForDisplay__ = {
    'קלוריות' : 'אנרגיה',
    'חלבונים': 'גרם',
    'פחמימות': 'גרם',
    'מתוכן סוכרים': 'גרם',
    'שומנים': 'גרם',
    'מתוכם שומן רווי': 'גרם',
    'כולסטרול': 'מ"ג',
    'נתרן': 'מ"ג',
    'סיבים תזונתיים': 'גרם',
    'מים': 'גרם',
    'ליקופן': 'מ"ג',
    'ויטמין A': 'מק"ג',
    'סה"כ ויטמין B': 'מ"ג',
    'ויטמין B1': 'מ"ג',
    'ויטמין B2': 'מ"ג',
    'ויטמין B3 - ניאצין': 'מ"ג',
    'ויטמין B5': 'מ"ג',
    'ויטמין B6': 'מ"ג',
    'חומצה פולית - ויטמין B9': 'מק"ג',
    'ויטמין C': 'מ"ג',
    'ויטמין E': 'מ"ג',
    'ויטמין K': 'מק"ג',
    'סידן': 'מ"ג',
    'ברזל': 'מ"ג',
    'מגנזיום': 'מ"ג',
    'זרחן': 'מ"ג',
    'אבץ': 'מ"ג',
    'אשלגן': 'מ"ג',
    'חומצות שומן רב בלתי רוויות': 'גרם',
    'חומצה אלפא לינולנית-אומגה 3': 'גרם',
    'חומצה לינולאית-אומגה 6': 'גרם',
    'חומצות שומן חד בלתי רוויות': 'גרם',
    'חומצת שומן אולאית-אומגה 9': 'גרם',
    'חלבונים': 'גרם',
    'פחמימות': 'גרם',
    'מתוכן סוכרים': 'גרם',
    'שומנים': 'גרם',
    'מתוכם שומן רווי': 'גרם',
    'כולסטרול': 'מ"ג',
    'נתרן': 'מ"ג',
    'סיבים תזונתיים': 'גרם',
    'מים': 'גרם',
    'ויטמין A': 'מק"ג',
    'סה"כ ויטמין B': 'מ"ג',
    'ויטמין B1': 'מ"ג',
    'ויטמין B2': 'מ"ג',
    'ויטמין B3 - ניאצין': 'מ"ג',
    'ויטמין B5': 'מ"ג',
    'ויטמין B6': 'מ"ג',
    'חומצה פולית - ויטמין B9': 'מק"ג',
    'ויטמין C': 'מ"ג',
    'ויטמין E': 'מ"ג',
    'ויטמין K': 'מק"ג',
    'סידן': 'מ"ג',
    'ברזל': 'מ"ג',
    'מגנזיום': 'מ"ג',
    'זרחן': 'מ"ג',
    'אבץ': 'מ"ג',
    'אשלגן': 'מ"ג',
    'חומצות שומן רב בלתי רוויות': 'גרם',
    'חומצה אלפא לינולנית-אומגה 3': 'גרם',
    'חומצה לינולאית-אומגה 6': 'גרם',
    'חומצות שומן חד בלתי רוויות': 'גרם',
    'חומצת שומן אולאית-אומגה 9': 'גרם'
}

__nutUnitsConversionToDisplay__ = dict()


GramConversionTable = {
    __gram__ : 1,
    __miliGram__ : 1e-3,
    __microGram__ : 1e-6,
    __kGram__ : 1e3,
    __tspn__ : 4.2
}

__dictNutUnitsForDisplayEng__ = dict()



def convertNutName(orgName):
    if nutNameList.__contains__(orgName):
        name = orgName
    else:
        raise Exception("Nutrition Name does not exists!")
    return name

def convertUnitToStandard(orgValueStr, orgUnits):
    units = __dictHebNameToEngName__[orgUnits]
    # Conver to gram if relevant
    value = float(orgValueStr)
    if (GramConversionTable.__contains__(units)):
        value = GramConversionTable[units]*value
        units = __gram__
    return [value,units]

def convertUnitFromStandard(Value, NewUnit):
    NewValue = Value
    standardUnitName = __dictHebNameToEngName__[NewUnit] # Covert to stadard unit name
    if GramConversionTable.__contains__(standardUnitName):
        NewValue[0] = Value[0]/GramConversionTable[standardUnitName]
    NewValue[1] = NewUnit;
    return NewValue

def debugPrintUniqueAttrInDataFrame(parsedItemExample,attrName):
    attrList = set()
    for item in parsedItemExample:
        attrList.add(parsedItemExample[item][attrName])
    for attr in attrList:
        print("'{}' : ".format(attr))



