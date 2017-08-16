# GridLatLong
Converts any latitude and longitude to a unique numerical global grid reference. Useful for quick lookups and indexing in a database like MySQL using standard equals or IN syntax.

Used by http://www.chicmi.com/ to provide a quick and dirty geospatial index for geographical entities.

PHP Usage
==============

Firstly import the file:

    require_once('GridLatLong.php');

To get the grid reference for any point:

    $gridlatlong = new JamieMBrown\GridLatLong\GridLatLong();
    var_dump($gridlatlong->getGridReferences(51.5155800, -0.1763000));
    # returns [ 826754 ]

Store this next to any latlong values you put into your database. It fits nicely into an INT in MySQL

To get all grid references in an area with a 30 kilometers radius around a point:

    $gridlatlong = new JamieMBrown\GridLatLong\GridLatLong();
    var_dump($gridlatlong->getGridReferences(51.5155800, -0.1763000, 30)); 
    # returns [ 822901, 824185, 825470, 824184, 825469, 826755, 825468, 826754, 828041, 826753, 828040, 829328 ]

Feed this into an IN statement in SQL to narrow down your query results efficiently, then apply your own bounding box / great circle query to get the absolute results.

By default GridLatLong splits the world into a 1000 x 1000 grid. To make the grid more or less granular, configure the size of the grid on initialisation:

    $gridlatlong = new JamieMBrown\GridLatLong\GridLatLong(10000);
    var_dump($gridlatlong->getGridReferences(51.5155800, -0.1763000)); 
    # returns [ 82649791 ]

By default GridLatLong takes distances in KM. Configure different units in initialisation, with 'M' being miles and 'N' being nautical miles:

    $gridlatlong = new JamieMBrown\GridLatLong\GridLatLong(1000, 'M');
    var_dump($gridlatlong->getGridReferences(51.5155800, -0.1763000, 5)); 
    # returns [ 825469, 826754 ]

Python Usage
==============

Firstly import the library:

    from gridlatlong import GridLatLong

To get the grid reference for any point:

    gll = GridLatLong()
    print gll.get_grid_references((51.5155800, -0.1763000))
    # returns [826754]

Store this next to any latlong values you put into your database. It fits nicely into an INT in MySQL

To get all grid references in an area with a 30 kilometers radius around a point:

    gll = GridLatLong()
    print gll.get_grid_references((51.5155800, -0.1763000), 30)
    # returns [822901, 824185, 825470, 824184, 825469, 826755, 825468, 826754, 828041, 826753, 828040, 829328]

Feed this into an IN statement in SQL to narrow down your query results efficiently, then apply your own bounding box / great circle query to get the absolute results.

By default GridLatLong splits the world into a 1000 x 1000 grid. To make the grid more or less granular, configure the size of the grid on initialisation:

    gll = GridLatLong(10000)
    print gll.get_grid_references((51.5155800, -0.1763000))
    # returns [82649791]

By default GridLatLong takes distances in KM. Configure different units in initialisation, with 'M' being miles and 'N' being nautical miles:

    gll = GridLatLong(1000, 'M')
    print gll.get_grid_references((51.5155800, -0.1763000), 5)
    # returns [825469, 826754]

The World is Not Flat
==============

GridLatLong splits the world into a 1000 x 1000 grid based on the latitude and longitude co-ordinates. This works well enough for most purposes but does mean that there are more grid references per KM at the poles, and fewer at the equator.