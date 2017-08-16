'''
 Converts Latitude and Longitude co-ordinates into a numerical grid, ideal for indexing in a relational database.
 By Jamie Brown (https://github.com/jamiembrown/GridLatLong)
 @license MIT
 '''

import math

class GridLatLong(object):

    grid_size = 1000
    distance_units = 'K'
    earth_radius = 6378.1

    def __init__(self, grid_size=1000, distance_units='K'):

        self.grid_size = grid_size
        self.distance_units = distance_units

        if self.distance_units == 'N':
            self.earth_radius = 3441.6147438
        elif self.distance_units == 'M':
            self.earth_radius = 3963.1676
        else:
            self.earth_radius = 6378.1

    def get_grid_references(self, lat_long, radius=0):

        bounding_north = self.get_internal_grid_reference(self.add_lat_long(lat_long, 0, radius))
        bounding_east = self.get_internal_grid_reference(self.add_lat_long(lat_long, 90, radius))
        bounding_south = self.get_internal_grid_reference(self.add_lat_long(lat_long, 180, radius))
        bounding_west = self.get_internal_grid_reference(self.add_lat_long(lat_long, 270, radius))
        references = []

        for lat in range(bounding_south[0], bounding_north[0] + 1):
            for lon in range(bounding_west[1], bounding_east[1] + 1):
                references.append(((lat + lon) * (lat + lon + 1)) / 2 + lon)

        return references

    def add_lat_long(self, lat_long, bearing, distance):
        lat1 = math.radians(float(lat_long[0]))
        lon1 = math.radians(float(lat_long[1]))
        brng = math.radians(float(bearing))
        dst = float(distance)

        lat2 = math.asin(math.sin(lat1) * math.cos(dst / self.earth_radius) + math.cos(lat1) * math.sin(dst / self.earth_radius) * math.cos(brng))
        lon2 = lon1 + math.atan2(math.sin(brng)*math.sin(dst / self.earth_radius)*math.cos(lat1), math.cos(dst / self.earth_radius)-math.sin(lat1) * math.sin(lat2))

        return (math.degrees(lat2), math.degrees(lon2))

    def get_internal_grid_reference(self, lat_long):
        grid_latitude = math.floor((float(self.grid_size) / 180.0) * (90.0 + float(lat_long[0])))
        grid_longitude = math.floor((float(self.grid_size) / 360.0) * (180.0 + float(lat_long[1])))
        return (int(grid_latitude), int(grid_longitude))

if __name__ == "__main__":
    gll = GridLatLong()

    print gll.get_grid_references((51.5155800, -0.1763000))
    print gll.get_grid_references((51.5155800, -0.1763000), 30)

    gll = GridLatLong(1000, 'M')

    print gll.get_grid_references((51.5155800, -0.1763000))
    print gll.get_grid_references((51.5155800, -0.1763000), 5)

