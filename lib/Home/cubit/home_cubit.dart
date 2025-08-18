import 'package:flutter/foundation.dart';

// State class for HomeScreen
class HomeState {
  final bool isArabic;
  final int selectedNavIndex;
  final bool isLoading;
  final List<Map<String, dynamic>> humanitarianCases;
  final List<Map<String, dynamic>> services;

  const HomeState({
    this.isArabic = true,
    this.selectedNavIndex = 0,
    this.isLoading = false,
    this.humanitarianCases = const [],
    this.services = const [],
  });

  HomeState copyWith({
    bool? isArabic,
    int? selectedNavIndex,
    bool? isLoading,
    List<Map<String, dynamic>>? humanitarianCases,
    List<Map<String, dynamic>>? services,
  }) {
    return HomeState(
      isArabic: isArabic ?? this.isArabic,
      selectedNavIndex: selectedNavIndex ?? this.selectedNavIndex,
      isLoading: isLoading ?? this.isLoading,
      humanitarianCases: humanitarianCases ?? this.humanitarianCases,
      services: services ?? this.services,
    );
  }
}

// Cubit using ChangeNotifier
class HomeCubit extends ChangeNotifier {
  HomeState _state = const HomeState();

  HomeState get state => _state;

  void onLanguageChanged(bool isArabic) {
    _state = _state.copyWith(isArabic: isArabic);
    notifyListeners();
  }

  void onNavItemSelected(int index) {
    _state = _state.copyWith(selectedNavIndex: index);
    notifyListeners();
  }

  void setLoading(bool isLoading) {
    _state = _state.copyWith(isLoading: isLoading);
    notifyListeners();
  }

  void loadHumanitarianCases() {
    setLoading(true);

    // Simulate API call
    Future.delayed(const Duration(seconds: 1), () {
      final cases = [
        {
          'id': 1,
          'title': 'ساند أهل غزة',
          'description':
              'في ظل دعمنا الكامل لأهالينا في فلسطين، اتبرع لدعم غزة لتقديم المساعدة الإنسانية',
          'imageUrl':
              'https://via.placeholder.com/400x150/FF6B6B/FFFFFF?text=Gaza+Support',
          'category': 'غارمين',
          'progress': 0.75,
        },
        {
          'id': 2,
          'title': 'دعم التعليم',
          'description': 'ساعد في توفير التعليم للأطفال المحتاجين',
          'imageUrl':
              'https://via.placeholder.com/400x150/4ECDC4/FFFFFF?text=Education',
          'category': 'تعليم',
          'progress': 0.45,
        },
      ];

      _state = _state.copyWith(humanitarianCases: cases, isLoading: false);
      notifyListeners();
    });
  }

  void loadServices() {
    final services = [
      {'icon': 'handshake', 'label': 'تطوير المجتمع', 'color': 'orange'},
      {'icon': 'school', 'label': 'التعليم', 'color': 'green'},
      {'icon': 'favorite', 'label': 'الصحة', 'color': 'green'},
      {'icon': 'people', 'label': 'التكافل الاجتماعي', 'color': 'orange'},
    ];

    _state = _state.copyWith(services: services);
    notifyListeners();
  }

  void initialize() {
    loadServices();
    loadHumanitarianCases();
  }

  void reset() {
    _state = const HomeState();
    notifyListeners();
  }
}
