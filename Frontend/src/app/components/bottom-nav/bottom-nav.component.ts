import { Component } from "@angular/core";
import { IconLabelComponent } from "../icon-label/icon-label.component";
import { APP_ROUTES } from "../../../constants/route.constants";
import { CreateButtonComponent } from "../create-button/create-button.component";

@Component({
  selector: "app-bottom-nav",
  imports: [IconLabelComponent, CreateButtonComponent],
  templateUrl: "./bottom-nav.component.html",
  styleUrl: "./bottom-nav.component.css",
})
export class BottomNavComponent {
  protected readonly ROUTES = APP_ROUTES;
}
