import 'package:ensan_test/core/colors.dart';
import 'package:flutter/material.dart';
import 'package:gap/gap.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          // Top Banner with Logo and Welcome Text
          Container(
            width: double.infinity,
            height: MediaQuery.of(context).size.height * 0.3,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [AppColors.primary, const Color(0xFF00695C)],
              ),
            ),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 15),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.start,
                children: [
                  Gap(50),
                  Align(
                    alignment: Alignment.centerRight,
                    child: const Text(
                      'أهلاً عزيزي المتبرع',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  Align(
                    alignment: Alignment.centerRight,
                    child: const Text(
                      'مرحباً بك في انسان',
                      style: TextStyle(color: Colors.white, fontSize: 18),
                    ),
                  ),
                  Directionality(
                    textDirection: TextDirection.rtl,
                    child: SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Container(
                        // clipBehavior: Clip.antiAlias,
                        decoration: BoxDecoration(
                          // color: Colors.white,
                          borderRadius: BorderRadius.circular(2),
                        ),
                        padding: const EdgeInsets.symmetric(
                          vertical: 20,
                          horizontal: 10,
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildServiceIcon(
                              Icons.people,
                              'التكافل ا...',
                              Colors.orange,
                            ),

                            Gap(10),
                            _buildServiceIcon(
                              Icons.school,
                              'التعليم',
                              Colors.green,
                            ),
                            Gap(10),

                            _buildServiceIcon(
                              Icons.favorite,
                              'الصحة',
                              Colors.green,
                            ),
                            Gap(10),

                            _buildServiceIcon(
                              Icons.fastfood,
                              'الغارمين',
                              Colors.red,
                            ),
                            Gap(10),

                            _buildServiceIcon(
                              Icons.local_hospital,
                              'مناحي الحياه ',
                              Colors.blue,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Main Content Area
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  // Main Promotional Banner
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.blue[50],
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Expanded(
                              child: Column(
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(
                                        Icons.person,
                                        color: Colors.blue[300],
                                        size: 40,
                                      ),
                                      const SizedBox(width: 8),
                                      Icon(
                                        Icons.medical_services,
                                        color: Colors.green,
                                        size: 40,
                                      ),
                                      const SizedBox(width: 8),
                                      Icon(
                                        Icons.family_restroom,
                                        color: Colors.pink[300],
                                        size: 40,
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 16),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 16,
                                      vertical: 8,
                                    ),
                                    decoration: BoxDecoration(
                                      color: Colors.green[600],
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: const Text(
                                      'مصر الخير',
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          'كل باب خير وراه مصر الخير',
                          style: TextStyle(color: Colors.green, fontSize: 16),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          'زكاتك وصدقتك مع مصر الخير',
                          style: TextStyle(
                            color: Colors.green,
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 16),
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          children: [
                            _buildTag('غارمين'),
                            _buildTag('تعليم'),
                            _buildTag('صحة'),
                            _buildTag('إطعام'),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 20),

                  // Humanitarian Cases Section
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'الحالات الإنسانية',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 16),
                      Container(
                        width: double.infinity,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.grey.withOpacity(0.2),
                              spreadRadius: 1,
                              blurRadius: 4,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(16),
                          child: Column(
                            children: [
                              Container(
                                height: 150,
                                decoration: BoxDecoration(
                                  image: DecorationImage(
                                    image: NetworkImage(
                                      'https://via.placeholder.com/400x150/FF6B6B/FFFFFF?text=Gaza+Support',
                                    ),
                                    fit: BoxFit.cover,
                                  ),
                                ),
                                child: Stack(
                                  children: [
                                    Positioned(
                                      right: 16,
                                      bottom: 16,
                                      child: Container(
                                        padding: const EdgeInsets.all(8),
                                        decoration: BoxDecoration(
                                          color: Colors.white.withOpacity(0.9),
                                          borderRadius: BorderRadius.circular(
                                            20,
                                          ),
                                        ),
                                        child: const Icon(
                                          Icons.class_,
                                          color: Colors.green,
                                          size: 24,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              Container(
                                padding: const EdgeInsets.all(16),
                                color: Colors.white,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    const Text(
                                      'ساند أهل غزة',
                                      style: TextStyle(
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 8),
                                    const Text(
                                      'في ظل دعمنا الكامل لأهالينا في فلسطين، اتبرع لدعم غزة لتقديم الم...',
                                      style: TextStyle(
                                        fontSize: 14,
                                        color: Colors.grey,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),

          // Bottom Navigation Bar
          Container(
            padding: const EdgeInsets.symmetric(vertical: 16),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.2),
                  spreadRadius: 1,
                  blurRadius: 4,
                  offset: const Offset(0, -2),
                ),
              ],
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildNavItem(Icons.grid_view, 'قائمة', false),
                _buildNavItem(Icons.flash_on, 'حساب الزكاة', false),
                _buildNavItem(Icons.favorite, 'تبرع', true),
                _buildNavItem(Icons.home, 'الرئيسية', true),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildServiceIcon(IconData icon, String label, Color color) {
    return Column(
      children: [
        Container(
          width: 60,
          height: 60,
          decoration: BoxDecoration(
            color: Colors.white,
            shape: BoxShape.rectangle,
            borderRadius: BorderRadius.circular(18),
            boxShadow: [
              BoxShadow(
                color: Colors.grey.withOpacity(0.2),
                spreadRadius: 1,
                blurRadius: 4,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Icon(icon, color: color, size: 30),
        ),
        Gap(8),
        Text(
          label,
          style: const TextStyle(
            fontSize: 15,
            color: Colors.white,
            fontWeight: FontWeight.w500,
          ),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }

  Widget _buildTag(String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.green[600],
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        text,
        style: const TextStyle(
          color: Colors.white,
          fontSize: 14,
          fontWeight: FontWeight.w500,
        ),
      ),
    );
  }

  Widget _buildNavItem(IconData icon, String label, bool isActive) {
    return Column(
      children: [
        Icon(icon, color: isActive ? Colors.green[600] : Colors.grey, size: 24),
        const SizedBox(height: 4),
        Text(
          label,
          style: TextStyle(
            fontSize: 12,
            color: isActive ? Colors.green[600] : Colors.grey,
          ),
        ),
      ],
    );
  }
}
