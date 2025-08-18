# Ensan Test - Flutter Authentication App

## Project Overview

**Ensan Test** is a comprehensive Flutter application that provides a complete authentication system with a modern and beautiful user interface. The app is built using the latest Flutter technologies and follows best practices in app development.

## Key Features

### 🔐 Authentication System
- **Login**: Secure login interface
- **User Registration**: New user registration
- **Forgot Password**: Password reset functionality
- **Reset Password**: Password update functionality

### 🎨 User Interface
- Modern and beautiful design
- Reusable components
- Consistent color scheme
- Enhanced user experience

### 🏗️ Technical Architecture
- **State Management**: Using Cubit for state management
- **Routing**: Advanced routing system
- **Provider Pattern**: Data and authentication management
- **Responsive Design**: Responsive design for all devices

## Technologies Used

- **Flutter**: Main framework
- **Dart**: Programming language
- **Cubit**: State management
- **Provider**: Data management
- **Material Design**: Material design system

## Project Structure

```
lib/
├── auth/                    # Authentication system
│   ├── cubit/              # Authentication state management logic
│   ├── screens/            # Authentication screens
│   └── user_provider.dart  # User data provider
├── components/              # Reusable components
├── core/                    # Core files
│   ├── colors.dart         # Color definitions
│   ├── constants/          # Constants
│   └── theme/              # Themes
├── Home/                    # Home screen
│   ├── cubit/              # Home screen logic
│   └── screens/            # App screens
├── router/                  # Routing system
└── main.dart               # Entry point
```

## Requirements

- Flutter SDK (latest stable version)
- Dart SDK
- Android Studio / VS Code
- Android emulator or iOS device

## How to Run

1. **Clone the project**
   ```bash
   git clone [repository URL]
   cd ensan_test
   ```

2. **Install dependencies**
   ```bash
   flutter pub get
   ```

3. **Run the app**
   ```bash
   flutter run
   ```

## Building the App

### For Android
```bash
flutter build apk --release
```

### For iOS
```bash
flutter build ios --release
```

### For Web
```bash
flutter build web
```

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the project
2. Create a new feature branch
3. Make your changes
4. Submit a Pull Request

## License

This project is licensed under [License Name] - see the LICENSE file for details.

## Contact Information

- **Developer**: [Your Name]
- **Email**: [Your Email]
- **GitHub**: [GitHub URL]

## Acknowledgments

Thanks to all contributors and developers who helped in developing this project.

---

**Note**: This project is in development. Some features may have changes or updates.
