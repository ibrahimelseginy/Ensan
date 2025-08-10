import 'package:ensan_test/components/custom_text.dart';
import 'package:ensan_test/core/colors.dart';
import 'package:flutter/material.dart';

class CustomAppbar extends StatefulWidget implements PreferredSizeWidget {
  CustomAppbar({super.key, required this.isArabic});

  bool isArabic = true; // true for Arabic, false for English
  void toggleLanguage() {
    isArabic = !isArabic;
  }

  @override
  State<CustomAppbar> createState() => _CustomAppbarState();

  @override
  // TODO: implement preferredSize
  Size get preferredSize => const Size.fromHeight(80);
}

class _CustomAppbarState extends State<CustomAppbar> {
  @override
  Size get preferredSize => const Size.fromHeight(80);

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: AppColors.primary,
      actions: [
        Column(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            GestureDetector(
              onTap: () {
                setState(() {
                  widget.isArabic = !widget.isArabic;
                });
              },
              child: Container(
                clipBehavior: Clip.antiAliasWithSaveLayer,
                width: 50,
                height: 50,
                decoration: BoxDecoration(
                  color: const Color.fromARGB(255, 212, 212, 212),
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: CustomText(
                    text: widget.isArabic ? "en" : "ع",
                    size: 18,
                    weight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
              ),
            ),
          ],
        ),
      ],
      leadingWidth: 25,
      centerTitle: true,
      scrolledUnderElevation: 0.0,
    );
  }
}
