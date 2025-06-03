import { Component } from "@angular/core";
import { AuthController } from "../../../controllers/auth.controller";

@Component({
  selector: "app-profile",
  imports: [],
  templateUrl: "./profile.component.html",
  styleUrl: "./profile.component.css",
})
export class ProfileComponent {
  constructor(protected readonly authController: AuthController) {}
}
