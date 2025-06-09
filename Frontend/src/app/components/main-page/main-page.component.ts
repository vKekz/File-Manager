import { Component } from "@angular/core";
import { MainBottomNavComponent } from "../main-bottom-nav/main-bottom-nav.component";
import { MainHeaderComponent } from "../main-header/main-header.component";
import { RouterOutlet } from "@angular/router";

@Component({
  selector: "app-main-page",
  imports: [MainBottomNavComponent, MainHeaderComponent, RouterOutlet],
  templateUrl: "./main-page.component.html",
  styleUrl: "./main-page.component.css",
})
export class MainPageComponent {}
