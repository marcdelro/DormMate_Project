# Background Images for DormMate

## Current Background Images Used:

### 1. Default (Modern Dormitory Interior)
- **URL**: https://images.unsplash.com/photo-1556742044-3c52d6e88c62
- **Description**: Modern, clean dormitory room interior
- **Color Overlay**: Purple gradient

### 2. Alternative Options (Commented in CSS):

#### Modern Dorm Room
- **URL**: https://images.unsplash.com/photo-1555854877-bab0e564b8d5
- **Description**: Cozy modern student room
- **Color Overlay**: Dark blue gradient

#### University Campus
- **URL**: https://images.unsplash.com/photo-1498243691581-b145c3f54a5a
- **Description**: Beautiful university campus building
- **Color Overlay**: Navy gradient

#### Students Studying
- **URL**: https://images.unsplash.com/photo-1522202176988-66273c2fd55f
- **Description**: Students collaborating and studying
- **Color Overlay**: Blue-purple gradient

#### University Building
- **URL**: https://images.unsplash.com/photo-1562774053-701939374585
- **Description**: Modern university architecture
- **Color Overlay**: Blue-purple gradient

## How to Change Background Images:

### Method 1: Use Different Unsplash Images
1. Go to https://unsplash.com
2. Search for "dormitory", "university", "student housing", etc.
3. Copy the image URL
4. Replace the URL in `assets/css/style.css` in the `.auth-branding` section

### Method 2: Use Local Images
1. Download your preferred image
2. Save it in `/assets/images/` folder
3. Update CSS to use: `url('../images/your-image.jpg')`

### Method 3: Switch Between Provided Options
- Uncomment any of the alternative background sections in the CSS file
- Comment out the current default background

## CSS Structure:
```css
.auth-branding {
    background: linear-gradient(overlay-color),
                url('image-url') center/cover;
}
```

The gradient overlay ensures text readability while maintaining the beautiful background image effect.
