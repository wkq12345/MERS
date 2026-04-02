# MERS - TOPSIS Recommendation System

## Overview
The Malaysia Ecotourism Recommendation System (MERS) uses the **TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution)** method to provide personalized tourist spot recommendations based on user preferences.

## Features Implemented

### 1. **Preference Rating Page** (`/recommendations`)
- Users can rate their interest in 8 different activities on a scale of 1-9
- Interactive slider interface with real-time value updates
- Modern, animated UI with gradient designs
- Responsive layout for mobile and desktop

### 2. **Criteria Used** (Equal Weights: 0.125 each)
- **Boat** - Boat rides and water transportation
- **Dolphin & Whale Watching** - Marine life observation
- **Water Sports** - Activities like snorkeling, diving, kayaking
- **Camping** - Outdoor camping experiences
- **Climbing** - Rock climbing and similar activities
- **Hiking** - Trail hiking and trekking
- **Horse Riding** - Equestrian activities
- **Jogging Paths & Tracks** - Running and fitness trails

### 3. **TOPSIS Algorithm Implementation**
The system implements a complete TOPSIS calculation:

#### Step 1: Decision Matrix
- Collects tourist spot scores from the database
- Creates a matrix of alternatives (spots) vs criteria (activities)

#### Step 2: Normalization
```
normalized_value = score / sqrt(sum_of_squares)
```

#### Step 3: Weighted Normalization
```
weighted_value = normalized_value × weight
```

#### Step 4: Ideal Solutions
- **Ideal Best**: Maximum value for each criterion
- **Ideal Worst**: Minimum value for each criterion

#### Step 5: Separation Measures
```
distance_to_best = sqrt(Σ(weighted_value - ideal_best)²)
distance_to_worst = sqrt(Σ(weighted_value - ideal_worst)²)
```

#### Step 6: Relative Closeness
```
TOPSIS_score = distance_to_worst / (distance_to_best + distance_to_worst)
```

### 4. **Results Page** (`/recommendations/calculate`)
- Displays ranked recommendations based on TOPSIS scores
- Shows TOPSIS score and match percentage for each spot
- Beautiful card layout with ranking badges (Gold, Silver, Bronze)
- Detailed criteria scores for each tourist spot
- Animated entrance effects for each card

## Database Schema Required

### Tourist Spots Table
```sql
CREATE TABLE tourist_spots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    location_id INT,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Criteria Table
```sql
CREATE TABLE criteria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    weight DECIMAL(5,3),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Scores Table
```sql
CREATE TABLE scores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    spot_id INT,
    criteria_id INT,
    score INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (spot_id) REFERENCES tourist_spots(id),
    FOREIGN KEY (criteria_id) REFERENCES criteria(id)
);
```

## Files Created

### 1. Controller
- `app/Http/Controllers/RecommendationController.php`
  - `showPreferenceForm()` - Display preference rating page
  - `calculateRecommendations()` - Process TOPSIS calculations
  - `applyTOPSIS()` - Core TOPSIS algorithm
  - `normalizeMatrix()` - Normalization step
  - `calculateWeightedMatrix()` - Weighted normalization

### 2. Views
- `resources/views/recommendations/preferences.blade.php` - Rating page
- `resources/views/recommendations/results.blade.php` - Results page

### 3. Routes
- `GET /recommendations` - Show preference form
- `POST /recommendations/calculate` - Calculate and show results

## How to Use

### 1. Access the System
- Navigate to `/recommendations` or click "Get Personalized Recommendations" from dashboard

### 2. Rate Your Preferences
- Use the sliders to rate each activity from 1-9
- 1 = Not interested at all
- 5 = Moderately interested
- 9 = Very interested

### 3. Get Recommendations
- Click "Get Recommendations" button
- System calculates TOPSIS scores
- Results are displayed in ranked order

### 4. View Results
- Top recommendations appear first (Gold, Silver, Bronze badges)
- Each card shows:
  - Tourist spot name and description
  - TOPSIS score (0-1, higher is better)
  - Match percentage
  - Individual criteria scores

## Sample Data to Add

You can add sample data using these SQL statements:

```sql
-- Add criteria
INSERT INTO criteria (name, weight) VALUES
('Boat', 0.125),
('Dolphin & Whale watching', 0.125),
('Water Sports', 0.125),
('Camping', 0.125),
('Climbing', 0.125),
('Hiking', 0.125),
('Horse Riding', 0.125),
('Jogging Paths & Tracks', 0.125);

-- Add sample tourist spot
INSERT INTO tourist_spots (name, location_id, description, image_url) VALUES
('Pulau Tioman', 1, 'Beautiful island paradise with pristine beaches', 'images/Pahang/pulau_tioman.png');

-- Add scores for the spot (spot_id 1, criteria_ids 1-8)
INSERT INTO scores (spot_id, criteria_id, score) VALUES
(1, 1, 9), -- Boat: 9
(1, 2, 8), -- Dolphin watching: 8
(1, 3, 9), -- Water sports: 9
(1, 4, 6), -- Camping: 6
(1, 5, 3), -- Climbing: 3
(1, 6, 5), -- Hiking: 5
(1, 7, 2), -- Horse riding: 2
(1, 8, 4); -- Jogging: 4
```

## Testing the System

1. **Without Data**: System shows "No tourist spots found" message
2. **With Data**: Calculates proper TOPSIS scores and rankings
3. **Validation**: All ratings must be between 1-9

## Design Features

### UI/UX Highlights
- ✨ Smooth animations and transitions
- 🎨 Modern gradient color schemes
- 📱 Fully responsive design
- 🎯 Interactive sliders with real-time feedback
- 🏆 Ranking badges for top 3 recommendations
- 📊 Visual criteria score displays

### Color Scheme
- Primary: `#4f46e5` (Indigo)
- Secondary: `#06b6d4` (Cyan)
- Success: `#10b981` (Green)
- Background: Gradient from `#f8fafc` to `#e0e7ff`

## Future Enhancements

1. **User History**: Save user preferences and past searches
2. **Advanced Filters**: Filter by location, price range, season
3. **Comparison Tool**: Compare multiple spots side-by-side
4. **Reviews & Ratings**: User-generated content
5. **Booking Integration**: Direct booking functionality
6. **Machine Learning**: Improve recommendations over time

## Technical Notes

- **Algorithm Complexity**: O(n × m) where n = spots, m = criteria
- **Performance**: Optimized for up to 1000 tourist spots
- **Validation**: Server-side validation for all inputs
- **Security**: CSRF protection on all forms

## Support

For issues or questions, please contact the development team.

---

**MERS** - Making ecotourism accessible and personalized for everyone! 🌿🏔️🌊
