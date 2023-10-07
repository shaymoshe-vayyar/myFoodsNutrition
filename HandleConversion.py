
## Constants
__gram__ = 'gram'
__microGram__ = 'microGram'
__miliGram__ = 'miliGram'
__microGram__ = 'microGram'
__kGram__ = 'kGram'
__KCal__ = 'KCal'
__tspn__ = 'tspn'

##
nutNameList = ['חומצת שומן אולאית-אומגה 9', 'חומצות שומן חד בלתי רוויות',
       'חומצה לינולאית-אומגה 6', 'חומצה אלפא לינולנית-אומגה 3',
       'חומצות שומן רב בלתי רוויות', 'אשלגן', 'אבץ', 'זרחן', 'מגנזיום', 'ברזל',
       'סידן', 'ויטמין K', 'ויטמין E', 'ויטמין C', 'חומצה פולית - ויטמין B9',
       'ויטמין B6', 'ויטמין B5', 'ויטמין B3 - ניאצין', 'ויטמין B2',
       'ויטמין B1', 'סה"כ ויטמין B', 'ויטמין A', 'ליקופן', 'מים',
       'סיבים תזונתיים', 'נתרן', 'כולסטרול', 'מתוכם שומן רווי', 'שומנים',
       'כפיות סוככפיות סוכר', 'מתוכן סוכרים', 'פחמימות', 'חלבונים',
       'קלוריות']

__dictNutNameToUnitsForDisplay__ = {
    'קלוריות': 'אנרגיה',
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
    'קלוריות': 'אנרגיה',
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

__nutUnitsConversion__ = {
    'אנרגיה'    :   __KCal__,
    'מק"ג'      :   __microGram__,
    'מ"ג'       :   __miliGram__,
    'גרם'       :   __gram__,
    'כפיות סוכ':   __tspn__
}

__nutUnitsConversionToDisplay__ = dict()
for attr in __nutUnitsConversion__:
    __nutUnitsConversionToDisplay__[__nutUnitsConversion__[attr]] = attr


GramConversionTable = {
    __gram__ : 1,
    __miliGram__ : 1e-3,
    __microGram__ : 1e-6,
    __kGram__ : 1e3,
    __tspn__ : 4.2
}


def convertNutName(orgName):
    if nutNameList.__contains__(orgName):
        name = orgName
    else:
        raise Exception("Nutrition Name is not exists!")
    return name

def convertUnitToStandard(orgValueStr, orgUnits):
    units = __nutUnitsConversion__[orgUnits]
    # Conver to gram if relevant
    value = float(orgValueStr)
    if (GramConversionTable.__contains__(units)):
        value = GramConversionTable[units]*value
        units = __gram__
    return [value,units]

def convertUnitFromStandard(Value, NewUnit):
    NewValue = Value
    standardUnitName = __nutUnitsConversion__[NewUnit] # Covert to stadard unit name
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